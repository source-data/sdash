<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Panel;
use App\Models\PanelLog;
use App\Models\License;
use App\Models\Group;
use Obiefy\API\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Notifications\PanelMadePublic;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Interfaces\PanelRepositoryInterface;
use App\Repositories\Interfaces\ImageRepositoryInterface;

class PanelController extends Controller
{

    protected $panelRepository;
    protected $imageRepository;

    /**
     * Use the PanelRepository to abstract the complexity of the panel request
     */
    public function __construct(ImageRepositoryInterface $imageRepository, PanelRepositoryInterface $panelRepository)
    {
        $this->panelRepository = $panelRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Panel $panel)
    {
    }

    /**
     * List panels available to the user, either because they own them, are an author, they're public or
     * they're shared via a group
     *
     * @param Request $request
     * @return APIResponse
     */
    public function listUserPanels(Request $request)
    {
        $user       = auth()->user();
        $search     = $request->input("search");
        $tags       = $request->input("keywords");
        $authors    = $request->input("authors");
        $sortOrder  = $request->input("sortOrder");
        $private    = $request->has("private");

        return API::response(
            200,
            "A list of panels accessible to the logged-in user.",
            $this->panelRepository->userPanels($user, $search, $tags, $authors, $sortOrder, $private)
        );
    }

    /**
     * List panels available to the user, either because they own them, they're public or
     * they're shared via a group
     *
     * @param Request $request
     * @return APIResponse
     */
    public function listGroupPanels(Request $request, Group $group)
    {
        $user       = auth()->user();
        $search     = $request->input("search");
        $tags       = $request->input("keywords");
        $authors    = $request->input("authors");
        $sortOrder  = $request->input("sortOrder");
        $private    = $request->has("private");
        $paginate   = $request->has("paginate") ? strtoupper($request->get("paginate")) === 'TRUE' : true;

        if (!$group->is_public && !Gate::allows('view-group', $group)) {
            return API::response(401, "Access denied", []);
        }

        if (Gate::allows('view-group', $group)) {
            return API::response(
                200,
                "A list of panels accessible to chosen group.",
                $this->panelRepository->groupPanels($user, $group, $search, $tags, $authors, $sortOrder, $private, $paginate)
            );
        } else {
            return API::response(
                200,
                "A list of public panels accessible to chosen group.",
                $this->panelRepository->publicGroupPanels($group, $search, $tags, $authors, $sortOrder)
            );
        }
    }

    /**
     * List only the public panels
     *
     * @param Request $request
     * @return void
     */
    public function listPublicPanels(Request $request)
    {
        $search     = $request->input('search');
        $tags       = $request->input("keywords") ?: $request->input("tags");
        $authors    = $request->input("authors");
        $sortOrder  = $request->input("sortOrder");

        return API::response(200, "A list of public panels.", $this->panelRepository->publicPanels($search, $tags, $authors, $sortOrder));
    }

    /**
     * List only the public panels of a public group
     *
     * @param Request $request
     * @return void
     */
    public function listPublicGroupPanels(Request $request, Group $group)
    {
        if (!$group->is_public) {
            return API::response(401, "Access denied.", []);
        }

        $search     = $request->input('search');
        $tags       = $request->input("keywords") ?: $request->input("tags");
        $authors    = $request->input("authors");
        $sortOrder  = $request->input("sortOrder");

        return API::response(200, "A list of public panels.", $this->panelRepository->publicGroupPanels($group, $search, $tags, $authors, $sortOrder));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $maxFileSizeInMegaBytes = 4;
        $maxFileSizeInKiloBytes = 4 * 1000;
        $rules = [
            'file' => ['required_without:url', "max:$maxFileSizeInKiloBytes"],
            'url'  => ['required_without:file', 'url'],
        ];
        $messages = [
            'file.max' => "SmartFigure images may not be larger than $maxFileSizeInMegaBytes MB",
        ];
        $this->validate($request, $rules, $messages);

        $user = auth()->user();

        $originalFilename = $request->file('file')->getClientOriginalName();
        $mimeType = $request->file('file')->getMimeType();
        $mimeSegments = explode("/", $mimeType);

        $newPanel = Panel::create([
            'title' => $originalFilename,
            'user_id'   => $user->id,
            'type'      => $mimeSegments[0],
            'subtype'   =>  $mimeSegments[1]
        ]);

        $createdImage = $this->imageRepository->storePanelImage($newPanel, $request->file('file'));
        $newPanel->authors()->attach($user->id, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 0]);

        return API::response(200, "Panel successfully created.", Panel::where('id', $newPanel->id)->with(['groups', 'tags', 'user', 'authors', 'externalAuthors'])->first());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Panel $panel)
    {
        if (Gate::allows('view-panel', $panel)) {
            return API::response(
                200,
                "Detailed view of Panel.",
                Panel::where('id', $panel->id)
                    ->with([
                        'user',
                        'accessToken',
                        'tags' => function ($query) {
                            $query->withPivot('id', 'origin', 'role', 'type', 'category');
                        },
                        'comments' => function ($query) {
                            $query->orderBy('created_at')->with('user');
                        },
                        'groups',
                        'authors',
                        'externalAuthors',
                        'files' => function ($query) {
                            $query->where('is_archived', false);
                        }
                    ])->get()
            );
        } else {
            return API::response(401, "Access denied.", []);
        }
    }

    /**
     * Display the specified public panel resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPublic(Request $request, Panel $panel)
    {
        $validator = Validator::make(
            $request->all(),
            ['token' => ['string', 'exists:panel_access_tokens,token']]
        );

        if ($validator->fails()) abort(401, "Access Denied");

        $token = $request->get('token', null);

        if (Gate::allows('view-single-panel', [$panel, $token])) {
            $panels = Panel::where('id', $panel->id)
                ->with([
                    'user' => function ($query) {
                        $query->select(["users.id", "firstname", "surname", "department_name", "institution_name", "role"]);
                    },
                    'tags' => function ($query) {
                        $query->withPivot('id', 'origin', 'role', 'type', 'category');
                    },
                    'groups' => function ($query) {
                        $query->where('is_public', true);
                    },
                    'authors'  => function ($query) {
                        $query->select(["users.id", "firstname", "surname", "department_name", "institution_name", "orcid", "email"]);
                    },
                    'externalAuthors' => function ($query) {
                        $query->select(["external_authors.id", "firstname", "surname", "department_name", "institution_name", "orcid"]);
                    },
                    'files' => function ($query) {
                        $query->where('is_archived', false);
                    }
                ])->get();
            foreach ($panels as $i => $panel) {
                foreach ($panel['authors'] as $j => $author) {
                    if ($author['author_role']['role'] !== User::PANEL_ROLE_CORRESPONDING_AUTHOR) {
                        unset($panels[$i]['authors'][$j]['email']);
                    }
                }
            }
            return API::response(
                200,
                "Detailed view of Public Panel.",
                $panels
            );
        } else {
            return API::response(401, "Access denied.", []);
        }
    }

    /**
     * List files of a public panel
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listPublicPanelFiles(Request $request, Panel $panel)
    {
        $categoryId = (int) $request->input('categoryId');

        if (Gate::allows('view-panel', $panel)) {
            $files = Panel::where('id', $panel->id)->first()->files();

            if ($categoryId) {
                $files->where('file_category_id', '=', $categoryId);
            }

            $files = $files->select('id', 'type', 'description', 'url', 'file_category_id AS category_id')->get();

            foreach ($files as $i => $file) {
                if ($file['type'] === 'file') {
                    $file['url'] = $request->getSchemeAndHttpHost() . '/files/' . $file['id'];
                }
                unset($file['type']);
                $files[$i] = $file;
            }

            return API::response(
                200,
                "A list of sources of a public panel.",
                $files
            );
        } else {
            return API::response(401, "Access denied.", []);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Panel $panel)
    {
        if (Gate::allows('modify-panel', $panel)) {

            $request->validate([
                "title"     =>  "nullable|max:255|min:1",
                "caption"   =>  "nullable|min:1",
            ]);

            $toUpdate = [];

            if ($request->has("title")) $toUpdate["title"] = strip_tags($request->input("title"));
            if ($request->has("caption")) $toUpdate["caption"] = strip_tags($request->input("caption"));

            $panel->update($toUpdate);

            return API::response(200, "Panel info updated.", $panel->load(['groups', 'user', 'authors', 'accessToken', 'externalAuthors']));
        } else {
            return API::response(401, "Permission denied.", []);
        }
    }

    /**
     * Update the status of a panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Panel $panel)
    {
        if (Gate::allows('modify-panel', $panel)) {

            $user = auth()->user();

            $request->validate([
                "is_public" => "boolean"
            ]);

            $toUpdate = [];

            if ($request->has("is_public")) $toUpdate["is_public"] = $request->input("is_public");

            $licenseId = null;

            if ($toUpdate["is_public"]) {
                $license = License::where('code', 'CC BY 4.0')->firstOrFail();
                $licenseId = $license->id;
            }

            $panel->update($toUpdate);

            PanelLog::create([
                'user_id' => auth()->user()->id,
                'panel_id' => $panel->id,
                'action_type' => ($toUpdate["is_public"] ? 'publish' : 'unpublish'),
                'license_id' => $licenseId,
            ]);

            if ($panel->is_public) {
                foreach ($panel->authors()->get() as $author) {
                    if ($author->id !== $user->id) {
                        $author->notify(new PanelMadePublic($user, $author, $panel));
                    }
                }
                foreach ($panel->externalAuthors()->get() as $externalAuthor) {
                    if ($externalAuthor->email) {
                        $externalAuthor->notify(new PanelMadePublic($user, $externalAuthor, $panel));
                    }
                }
            }

            return API::response(200, "Panel status updated.", $panel->is_public);
        } else {
            return API::response(401, "Permission denied.", []);
        }
    }

    /**
     * Change an image for a given panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $panelId
     * @return \Illuminate\Http\Response
     */
    public function changeImage(Request $request, int $panelId)
    {
        $request->validate([
            'file' => ['required', 'mimes:jpeg,png,jpg,gif,pdf,tif', 'max:4096']
        ]);

        $originalFilename = $request->file('file')->getClientOriginalName();

        $panel = Panel::find($panelId);

        $this->imageRepository->archiveAndRemove($panel->image);
        $this->imageRepository->storePanelImage($panel, $request->file);

        return API::response(200, "Panel image changed. ", $panel->version);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Panel $panel)
    {

        if (Gate::allows('modify-panel', $panel)) {

            $this->panelRepository->destroyPanel($panel);

            return API::response(200, "Panel deleted along with all related files.", []);
        } else {
            return API::response(401, "Permission denied.", []);
        }
    }


    public function deleteMultiple(Request $request)
    {
        $request->validate([
            "id"    => "required",
            "id.*"  => "exists:panels,id"
        ]);

        foreach ($request->get("id") as $panelId) {

            $panel = Panel::find($panelId);

            if (Gate::allows('modify-panel', $panel)) {
                $this->panelRepository->destroyPanel($panel);
            } else {
                return API::response(401, "Permission denied for panel {$panelId}.", []);
            }
        }

        return API::response(200, "Panels deleted", []);
    }
}
