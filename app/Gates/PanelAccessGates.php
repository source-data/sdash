<?php

namespace App\Gates;
use App\Models\Panel;
use App\User;

class PanelAccessGates
{

    public function canAccessPanel(User $user, Panel $panel)
    {
        $userId = $user->id;
        if($user->is_superadmin()) return true;

        // is the logged-in user the panel owner?
        if($userId === $panel->user_id) return true;

        // is the user part of a group that can access the panel?
        return Panel::where('id',$panel->id)->whereHas('groups', function($query) use($userId){
            $query->whereHas('confirmedUsers', function($query) use($userId){
                $query->where('users.id', $userId);
            });
        })->exists();

    }

    public function canViewPanel(User $user = null, Panel $panel)
    {
        if ($panel->made_public_at) return true;
        if(!$user) return false;
        return $this->canAccessPanel($user, $panel);

    }

    public function canModifyPanel(User $user, Panel $panel)
    {
        if($user->is_superadmin()) return true;

        // is the logged-in user the panel owner?
        if($user->id === $panel->user_id) return true;

        return false;
    }

}