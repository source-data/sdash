<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Group;
use App\Models\Panel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\UserAddedToGroup;

class GroupController extends Controller
{
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

        $newGroup->users()->attach($user->id,['role' => 'admin', 'status' => 'confirmed']);

        if($members){
            foreach($members as $member) {
                $token = sha1(now()->timestamp . $member["id"] . Str::random(24));
                $newGroup->users()->attach($member["id"],['role' => ($member["admin"]==TRUE) ? 'admin' : 'user', 'token' => $token]);
                $user = User::find($member["id"]);
                $user->notify( new UserAddedToGroup($user, $newGroup, $token) );
            }
        }

        if($panels){
            foreach($panels as $panel) {
                $newGroup->panels()->attach($panel);
            }
        }

        $newGroup->load(['confirmedUsers' => function($query) {
            $query->withPivot(['role']);
        }])->withCount(['confirmedUsers', 'panels']);

        return API::response(200, "Group created", $newGroup);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
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
