<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Panel;
use App\Models\Group;

interface PanelRepositoryInterface {
    public function userPanels(User $user, string $search=null, Array $tags=null, bool $private = false);
    public function GroupPanels(User $user, Group $group, string $search=null, Array $tags=null, bool $private = false);
    public function publicPanels(string $search=null, Array $tags=null);
}