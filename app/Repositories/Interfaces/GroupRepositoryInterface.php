<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Group;
use App\Models\Panel;
use Illuminate\Support\Str;
use App\Notifications\UserAddedToGroup;

interface GroupRepositoryInterface
{

    public function removeMemberFromGroup(Group $group, User $member);
    public function addMemberToGroup(Group $group, array $member);
    public function makeMemberIntoAdmin(Group $group, String $user_id);
    public function makeMemberNotAdmin(Group $group, String $user_id);
    public function applyToGroup(Group $group, User $user);
    public function acceptMembershipRequest(Group $group, String $user_id, String $new_status);
}
