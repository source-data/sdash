<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Group;
use App\Models\Panel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Repositories\GroupRepository;
use App\Notifications\UserAddedToGroup;
use App\Notifications\UserAppliedToJoinGroup;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use FFI;

class GroupController extends Controller
{

    protected $groupRepository;

    /**
     * Use the GroupRepository to abstract the complexity of the group request
     */
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * List only the public panels
     *
     * @param Request $request
     * @return void
     */
    public function listPublicGroups(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $query = Group::where('is_public', true)
            ->where("name", "like", "%{$search}%")
            ->orWhere("description", "like", "%{$search}%");
        }
        else {
            $query = Group::where('is_public', true);
        }

        $groups = $query->with([
                'administrators' => function ($query) {
                    $query->select('users.id', 'firstname', 'surname', 'department_name', 'institution_name', 'orcid', 'email');
                },
                'publicPanels' => function ($query) {
                    $query->select(['panels.id', 'title', 'version']);
                },
            ])
            ->withCount(['confirmedUsers', 'publicPanels', 'requestedUsers'])
            ->get();
        return API::response(200, "A list of public groups", $groups);
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
            'name'          => ['required', 'min:5'],
            'url'           => ['nullable', 'url'],
            'description'   => ['required'],
            'is_public'     => ['boolean'],
            'members.*.id'  => ['exists:users'],
            'panels.*'      => ['exists:panels,id'],
        ]);

        $user = auth()->user();
        $panels = $request->input("panels") ?? [];
        $members = $request->input("members") ?? [];

        $newGroup = Group::create([
            'user_id'       => $user->id,
            'name'          => $request->input("name"),
            'description'   => $request->input("description"),
            'url'           => $request->input("url"),
            'is_public'     => $request->input("is_public", false),
        ]);

        // add logged in user as admin
        $newGroup->users()->attach($user->id, ['role' => 'admin', 'status' => 'confirmed']);

        if ($members) {
            foreach ($members as $member) {
                $this->groupRepository->addMemberToGroup($newGroup, $member);
            }
        }

        if ($panels) {
            foreach ($panels as $panel) {
                $newGroup->panels()->attach($panel);
            }
        }

        $newGroup->load(['confirmedUsers' => function ($query) {
            $query->withPivot(['role', 'token', 'status']);
        }])->loadCount(['confirmedUsers', 'panels']);

        return API::response(200, "Group created", $newGroup);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Group $group)
    {
        // only the group admin can view unconfirmed users
        if ($request->get('unconfirmed_users') && !Gate::allows("modify-group", $group)) return API::response(403, "Admin level access denied", []);
        if (!Gate::allows("view-group", $group)) return API::response(403, "Access denied", []);

        if ($request->get('unconfirmed_users')) {
            $group->load(['users' => function ($query) {
                $query->withPivot(['role', 'status']);
            }])->loadCount(['users', 'panels']);
        } else {
            $group->load(['confirmedUsers' => function ($query) {
                $query->withPivot(['role', 'token', 'status']);
            }])->loadCount(['confirmedUsers', 'panels']);
        }

        return API::response(200, "Group loaded", $group);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Manage panels associated with a group
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function managePanels(Request $request, Group $group)
    {
        $user = auth()->user();

        $request->validate([
            'action' => ['required'],
            'panels' => ['required'],
            'panels.*' => ['exists:panels,id']
        ]);

        if (Gate::allows('view-group', $group)) {

            $action = $request->input('action');
            $panels = $request->input('panels');

            $affectedPanelIds = [];
            $responseMessage = '%u panels ';

            switch ($action) {
                case 'add':
                    $responseMessage .= 'added';
                    break;
                case 'remove':
                    $responseMessage .= 'removed';
                    break;
                default:
                    return API::response(400, "Invalid action", []);
            }

            $responseMessage .= '. %u skipped.';

            foreach ($panels as $panel) {
                $panelModel = Panel::findOrFail($panel);
                if (Gate::allows('modify-panel', $panelModel)) {
                    $panelExists = $group->panels()->where('panels.id', $panelModel->id)->exists();
                    switch ($action) {
                        case 'add':
                            if (!$panelExists) {
                                $group->panels()->attach($panelModel->id);
                                $affectedPanelIds[] = $panelModel->id;
                            }
                            break;
                        case 'remove':
                            if ($panelExists) {
                                $group->panels()->detach($panelModel->id);
                                $affectedPanelIds[] = $panelModel->id;
                            }
                            break;
                    }
                }
            }

            $affectedPanelsCount = count($affectedPanelIds);
            $ignoredPanelsCount = count($panels) - $affectedPanelsCount;

            return API::response(
                200,
                sprintf($responseMessage, $affectedPanelsCount, $ignoredPanelsCount),
                [
                    "group" => Group::where('id', $group->id)->with(['confirmedUsers' => function ($query) {
                        $query->withPivot(['role', 'token', 'status']);
                    }])->withCount(['confirmedUsers', 'panels', 'requestedUsers'])->first(),
                    "panels" => Panel::whereIn('id', $affectedPanelIds)->with(['accessToken', 'groups', 'tags', 'user', 'authors', 'externalAuthors'])->get()
                ]
            );
        } else {
            return API::response(403, "You are not an administrator of the group", []);
        }
    }

    /**
     * replace an existing Group details with a revised version of the data
     *
     * @param Request $request
     * @param Group $group
     * @return void
     */
    public function replace(Request $request, Group $group)
    {
        $user = auth()->user();
        $request->validate([
            'name'                  => ['required', 'min:5'],
            'url'                   => ['nullable', 'url'],
            'description'           => ['required'],
            'is_public'             => ['boolean'],
            'members.*.id'          => ['exists:users'],
            'members.*.status'      => Rule::in(['pending', 'requested', 'confirmed']),
            'panels.*'              => ['exists:panels,id']
        ]);

        if (Gate::allows('modify-group', $group)) {

            $group->load('users');

            $group->name = $request->input('name');

            $group->url = $request->input('url');

            $group->description = $request->input('description');

            $group->is_public = $request->input('is_public');

            //make sure all the admins aren't being removed
            if (!array_filter($request->input('members'), function ($members) {
                return $members["admin"] == true;
            })) return API::response(403, "A group must have an admin.", []);


            // detach removed members and panels
            foreach ($group->users as $existingMember) {

                if (count(array_filter($request->input('members'), function ($newMember) use ($existingMember) {
                    return $newMember["id"] === $existingMember->id;
                })) < 1) {

                    // remove member *and their panels*
                    $this->groupRepository->removeMemberFromGroup($group, $existingMember);
                }
            }

            // or attach members who are new to the group
            foreach ($request->input('members') as $newMember) {

                if (count(array_filter($group->users->toArray(), function ($existingUser) use ($newMember) {
                    return $existingUser["id"] === $newMember["id"];
                })) < 1) {

                    $this->groupRepository->addMemberToGroup($group, $newMember);
                } else {

                    // accept any newly accepted users to the group
                    $this->groupRepository->acceptMembershipRequest($group, $newMember["id"], $newMember["status"]);

                    // if the new member (submitted) has a different group role (user / admin) from the existing (stored) member
                    // update the stored member's status to match the submitted one.
                    if (isset($newMember["admin"])) {

                        switch ($newMember["admin"]) {
                            case true:
                                $this->groupRepository->makeMemberIntoAdmin($group, $newMember["id"]);
                                break;

                            case false:
                                $this->groupRepository->makeMemberNotAdmin($group, $newMember["id"]);
                                break;
                        }
                    }
                }
            }

            // loading group panels here as they may have been modified by
            // the process above
            if ($request->has('panels')) {

                $group->load('panels');

                foreach ($group->panels as $existingPanel) {

                    if (!in_array($existingPanel->id, $request->input('panels'))) {
                        $group->panels()->detach($existingPanel);
                    }
                }
            }

            // save changes
            $group->save();

            return API::response(
                200,
                "Panel Updated.",
                [
                    "group" => Group::where('id', $group->id)->with(['confirmedUsers' => function ($query) {
                        $query->withPivot(['role', 'token', 'status']);
                    }])->withCount(['confirmedUsers', 'panels', 'requestedUsers'])->first(),
                    "panels" => $group->panels()->with(['groups', 'tags', 'user'])->get()
                ]
            );
        } else {
            return API::response(403, "You are not an administrator of the group.", []);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $user = auth()->user();

        if (Gate::allows('modify-group', $group)) {
            $group->users()->detach();
            $group->panels()->detach();
            $group->delete();
            return API::response(200, "The group was deleted", []);
        } else {
            return API::response(403, "You are not the owner of the group.", []);
        }
    }

    public function join(Group $group, String $token)
    {
        $user = $group->users()->wherePivot("token", "=", $token)->first();

        if (empty($user)) return redirect('/');

        $group->users()->updateExistingPivot($user->id, ["status" => "confirmed", "token" => null]);
        return view('addtogroup', ['user' => $user, 'group' => $group]);
    }

    public function joinViaApi(Group $group, String $token)
    {
        $user = auth()->user();
        $userRecord = $group->users()->wherePivot("token", "=", $token)->first();

        if (!$user->is($userRecord)) return API::response(403, "Permission denied.", []);

        $group->users()->updateExistingPivot($user->id, ["status" => "confirmed", "token" => null]); //,
        return API::response(200, "Group updated.", [
            "group" => $user->groups()->where('groups.id', $group->id)->with(['confirmedUsers' => function ($query) {
                $query->withPivot(['role']);
            }])->withCount(['confirmedUsers', 'panels', 'requestedUsers'])->withPivot(["role", "status", "token"])->first()
        ]);
    }

    public function declineGroupInvitation(Group $group, String $token)
    {
        $user = auth()->user();

        // a user can only remove themselves and only from the pending state
        if ($user && $group->users()->where('users.id', $user->id)->wherePivot("status", "pending")->wherePivot("token", $token)->exists()) {
            $group->users()->detach($user->id);
            return API::response(200, "User removed from group.", []);
        } else {
            return API::response(403, "You cannot decline this group invitation.", []);
        }
    }



    /**
     * Allows an authenticated user to apply to join a panel group
     *
     * @param Group $group
     * @return void
     */
    public function apply(Group $group)
    {
        $user = auth()->user();

        try {
            $token = $this->groupRepository->applyToGroup($group, $user);
            $groupAdmins = $group->users()->wherePivot('role', 'admin')->get();
            foreach ($groupAdmins as $groupAdmin) {
                $groupAdmin->notify(new UserAppliedToJoinGroup($user, $group, $token));
            }
            return API::response(200, "You have applied to join the group.", [
                "group" => $user->groups()
                    ->where('groups.id', $group->id)
                    ->with(['confirmedUsers' => function ($query) {
                        $query->withPivot(['role']);
                    }])
                    ->withCount(['confirmedUsers', 'panels', 'requestedUsers'])
                    ->withPivot(["role", "status", "token"])
                    ->first()
            ]);
        } catch (\App\Exceptions\UserAlreadyAppliedToGroupException $e) {
            return API::response(400, "You have  already applied to join the group.", []);
        } catch (\App\Exceptions\UserAlreadyInGroupException $e) {
            return API::response(400, "You are already a group member.", []);
        }
    }

    /**
     * Accepts a user into a panel group by changing their status from requested to confirmed
     *
     * @param Group $group
     * @param string $token
     * @return void
     */
    public function acceptUser(Group $group, string $token)
    {
        $user = auth()->user();

        if (Gate::allows('modify-group', $group)) {
            $userRecord = $group->users()->wherePivot("token", "=", $token)->first();
            // if user is already accepted, return to group page
            if (!isset($userRecord)) return redirect('/group/' . $group->id);

            $group->users()->updateExistingPivot($userRecord->id, ["status" => "confirmed", "token" => null]);
            return redirect('/group/' . $group->id);
        } else {
            throw new \Exception("You are not permitted to perform this action.");
        }
    }
}
