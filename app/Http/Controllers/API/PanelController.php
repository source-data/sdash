<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Panel;
use App\Models\Image;
use App\Models\Group;
use Obiefy\API\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Repositories\ImageRepository;
use App\Repositories\PanelRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\PdfToImage\Pdf as PdfConverter;
use Intervention\Image\Facades\Image as ImageService;
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
     * List panels available to the user, either because they own them, they're public or
     * they're shared via a group
     *
     * @param Request $request
     * @return APIResponse
     */
    public function listUserPanels(Request $request)
    {
        $user       = auth()->user();
        $search     = $request->input("search");
        $tags       = $request->input("tags");
        $private    = $request->has("private");

        return API::response(
            200,
            "A list of panels accessible to the logged-in user.",
            $this->panelRepository->userPanels($user, $search, $tags, $private)
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
        $tags       = $request->input("tags");
        $private    = $request->has("private");

        if (!Gate::allows('view-group', $group)) return API::response(401, "Permission denied", []);

        return API::response(
            200,
            "A list of panels accessible to chosen group.",
            $this->panelRepository->groupPanels($user, $group, $search, $tags, $private)
        );
    }

    /**
     * List only the public panels
     *
     * @param Request $request
     * @return void
     */
    public function listPublicPanels(Request $request)
    {
        $search = $request->input('search');
        $tags   = $request->input("tags");

        return API::response(200, "A list of public panels.", $this->panelRepository->publicPanels($search, $tags));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:jpeg,png,jpg,gif,pdf,tif', 'max:4096']
        ]);

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

        return API::response(200, "Panel successfully created.", Panel::where('id', $newPanel->id)->with(['groups', 'tags', 'user'])->first());
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
                "title"         =>      "nullable|max:255|min:1",
                "caption"       =>      "nullable|min:1"
            ]);

            $toUpdate = [];

            if ($request->has("title")) $toUpdate["title"] = strip_tags($request->input("title"));
            if ($request->has("caption")) $toUpdate["caption"] = strip_tags($request->input("caption"));

            $panel->update($toUpdate);

            return API::response(200, "Panel info updated.", $panel->load(['groups', 'user']));
        } else {
            return API::response(401, "Permission denied.", []);
        }
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
