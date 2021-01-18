<?php

namespace App\Gates;

use Illuminate\Support\Facades\Log;
use App\Models\Panel;
use App\User;

class PanelAccessGates
{

    public function canAccessPanel(User $user, Panel $panel)
    {
        $userId = $user->id;
        if ($user->is_superadmin()) return true;

        // is the logged-in user the panel owner?
        if ($userId === $panel->user_id) return true;

        // is the user part of a group that can access the panel?
        return Panel::where('id', $panel->id)->whereHas('groups', function ($query) use ($userId) {
            $query->whereHas('confirmedUsers', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            });
        })->orWhereHas('authors', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->exists();
    }

    public function canViewPanel(User $user = null, Panel $panel)
    {
        if ($panel->made_public_at) return true;
        if (!$user) return false;
        return $this->canAccessPanel($user, $panel);
    }

    public function canModifyPanel(User $user, Panel $panel)
    {
        if ($user->is_superadmin()) return true;

        if ($this->isPanelCuratorOrCorrespondingAuthor($user, $panel)) return true;

        // is the logged-in user the panel owner?
        if ($user->id === $panel->user_id) return true;

        return false;
    }

    public function canModifyPanelTags(User $user, Panel $panel)
    {
        if ($user->is_superadmin()) return true;

        $userId = $user->id;

        // is the logged-in user the panel owner?
        if ($userId === $panel->user_id) return true;

        if ($this->isPanelCuratorOrCorrespondingAuthor($user, $panel)) return true;

        // is the user an admin of a group to which the panel belongs
        return Panel::where('id', $panel->id)->whereHas('groups', function ($query) use ($userId) {
            $query->whereHas('confirmedUsers', function ($query) use ($userId) {
                $query->where('users.id', $userId)->where('group_user.role', 'admin');
            });
        })->exists();
    }

    /**
     * Authorise a user to access a single-panel summary page.
     *
     * Logged in users with permission to view the panel will have access, but
     * on-authenticated users who pass in an access token can also view the panel.
     *
     * @param User|null $user
     * @param Panel $panel
     * @param String|null $token
     * @return boolean
     */
    public function canViewSinglePanelPage(?User $user, Panel $panel, ?String $token)
    {
        if (!$user) {

            // if the panel is a public panel, allow access
            if ($panel->made_public_at) return true;

            // no access token was submitted by the user
            if (!$token) return false;

            $accessToken = $panel->accessToken;

            // the panel has no access token
            if (!$accessToken) return false;

            return ($token === $accessToken->token);
        } else {
            return $this->canViewPanel($user, $panel);
        }
    }

    /**
     * The corresponding author or curator of a panel can edit the panel's metadata. This
     * protected method checks whether the user holds one of these roles on the given panel.
     *
     * @param User $user
     * @param Panel $panel
     * @return boolean true indicates that the user is either a corresponding author or curator.
     */
    protected function isPanelCuratorOrCorrespondingAuthor(User $user, Panel $panel): bool
    {
        return $panel->authors()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', [User::PANEL_ROLE_CORRESPONDING_AUTHOR, User::PANEL_ROLE_CURATOR])
            ->exists();
    }
}
