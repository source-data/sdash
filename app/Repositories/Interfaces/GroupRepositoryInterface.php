<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Group;
use App\Models\Panel;
use Illuminate\Support\Str;
use App\Notifications\UserAddedToGroup;

interface GroupRepositoryInterface {

    public function removeMemberFromGroup(Group $group, User $member);
    public function addMemberToGroup(Group $group, Array $member);

}