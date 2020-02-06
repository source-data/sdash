<?php

namespace App\Repositories;

use App\User;
use App\Models\Panel;
use App\Models\Group;
use Illuminate\Support\Str;
use App\Notifications\UserAddedToGroup;
use App\Repositories\Interfaces\GroupRepositoryInterface;

class GroupRepository implements GroupRepositoryInterface
{

    public function addMemberToGroup(Group $group, Array $member)
    {
        $token = sha1(now()->timestamp . $member["id"] . Str::random(24));

        $group->users()->attach($member["id"],['role' => ($member["admin"]==TRUE) ? 'admin' : 'user', 'token' => $token]);

        $user = User::find($member["id"]);

        $user->notify( new UserAddedToGroup($user, $group, $token) );

        return true;
    }

    public function removeMemberFromGroup(Group $group, User $member)
    {

        $group->users()->detach($member);

        $panelsToDetach = $group->panels()->where('user_id', $member->id)->get();

        $group->panels()->detach($panelsToDetach);

        return true;

    }

}