<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Panel;
use App\Models\Group;

interface PanelRepositoryInterface
{
    public function userPanels(User $user, string $search = null, array $tags = null, array $authors = null, string $sortOrder = '', bool $private = false);
    public function groupPanels(User $user, Group $group, string $search = null, array $tags = null, array $authors = null, string $sortOrder = '', bool $private = false);
    public function publicPanels(string $search = null, array $tags = null, array $authors = null, string $sortOrder = '');
    public function destroyPanel(Panel $panel);
}
