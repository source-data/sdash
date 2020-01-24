<?php

namespace App\Gates;
use App\Models\Group;
use App\User;
use Illuminate\Support\Facades\Log;

class GroupAccessGates
{
    public function canModifyGroup(User $user, Group $group)
    {
        return $group->confirmedUsers()->where('users.id', $user->id)->wherePivot('role', 'admin')->exists();
    }

    public function canViewGroup(User $user, Group $group)
    {
        return $group->confirmedUsers()->where('users.id', $user->id)->exists();
    }
}
