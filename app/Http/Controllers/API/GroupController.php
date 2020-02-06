<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Group;
use App\Models\Panel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Repositories\GroupRepository;
use App\Notifications\UserAddedToGroup;
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
            'members.*.id'  => ['exists:users'],
            'panels.*'      => ['exists:panels,id']

        ]);

        $user = auth()->user();
        $panels = $request->input("panels") ?? [];
        $members = $request->input("members") ?? [];

        $newGroup = Group::create([
            'user_id'       => $user->id,
            'name'          => $request->input("name"),
            'description'   => $request->input("description"),
            'url'           => $request->input("url")
        ]);

        // add logged in user as admin
        $newGroup->users()->attach($user->id,['role' => 'admin', 'status' => 'confirmed']);

        if($members){
            foreach($members as $member) {
                $this->groupRepository->addMemberToGroup($newGroup, $member);
            }
        }

        if($panels){
            foreach($panels as $panel) {
                $newGroup->panels()->attach($panel);
            }
        }

        $newGroup->load(['confirmedUsers' => function($query) {
            $query->withPivot(['role']);
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
        if($request->get('unconfirmed_users') && !Gate::allows("modify-group", $group)) return API::response(401, "Admin level access denied", []);
        if(!Gate::allows("view-group", $group)) return API::response(401, "Access denied", []);

        if($request->get('unconfirmed_users')) {
            $group->load(['users' => function($query) {
                $query->withPivot(['role', 'status']);
            }])->loadCount(['users', 'panels']);
        } else {
            $group->load(['confirmedUsers' => function($query) {
                $query->withPivot(['role']);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $user = auth()->user();

        $request->validate([
            'panels'  => ['required'],
            'panels.*' => ['exists:panels,id']
        ]);

        if(Gate::allows('modify-group', $group)) {

            $addedCount = 0;
            $notAddedCount = 0;

            foreach($request->input("panels") as $panel) {

                $panelModel = Panel::findOrFail($panel);

                if(Gate::allows('modify-panel', $panelModel)){


                    if(!$group->panels()->where('panels.id', $panelModel->id)->exists()){
                        $group->panels()->attach($panelModel->id);
                        $addedCount++;
                    } else {
                        $notAddedCount++;
                    }
                } else {
                    $notAddedCount++;
                }

            }

            return API::response(200, "$addedCount panels added. $notAddedCount skipped.",
                [
                    "group" => Group::where('id',$group->id)->with(['confirmedUsers' => function($query) { $query->withPivot(['role']); } ])->withCount(['confirmedUsers', 'panels'])->first(),
                    "panels" => $group->panels()->with(['groups', 'tags', 'user'])->get()
                ]
            );


        } else {
            return API::response(401, "You are not an administrator of the group.",[]);
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
            'members.*.id'          => ['exists:users'],
            'panels.*'              => ['exists:panels,id']
        ]);

        if(Gate::allows('modify-group', $group)) {

            $group->load('users');

            $group->name = $request->input('name');

            $group->url = $request->input('url');

            $group->description = $request->input('description');

            // detach removed members and panels
            foreach($group->users as $existingMember) {

                if(count(array_filter($request->input('members'), function($newMember) use($existingMember){ return $newMember["id"] === $existingMember->id; })) < 1) {

                    // remove member *and their panels*
                    $this->groupRepository->removeMemberFromGroup($group, $existingMember);

                }
            }

            // or attach members who are new to the group
            foreach($request->input('members') as $newMember) {

                if(count(array_filter($group->users, function($existingUser) use($newMember) { return $existingUser->id === $newMember["id"]; } )) < 1) {

                    $this->groupRepository->addMemberToGroup($group, $newMember);

                }
            }

            // loading group panels here as they may have been modified by
            // the process above
            if($request->input('panels')) {

                $group->load('panels');

                foreach($group->panels as $existingPanel) {

                    if( !in_array($existingPanel->id, $request->input('panels')) ) {
                        $group->panels()->detach($existingPanel);
                    }
                }

            }

            return API::response(200, "Panel Updated.",
                [
                    "group" => Group::where('id',$group->id)->with(['confirmedUsers' => function($query) { $query->withPivot(['role']); } ])->withCount(['confirmedUsers', 'panels'])->first(),
                    "panels" => $group->panels()->with(['groups', 'tags', 'user'])->get()
                ]
            );


        } else {
            return API::response(401, "You are not an administrator of the group.",[]);
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
        //
    }



    public function join(Group $group, String $token)
    {
        $user = $group->users()->wherePivot("token","=", $token)->firstOrFail();
        $group->users()->updateExistingPivot($user->id, ["status" => "confirmed"]); //, "token" => null
        return view('addtogroup',['user' => $user, 'group' => $group]);
    }
}
