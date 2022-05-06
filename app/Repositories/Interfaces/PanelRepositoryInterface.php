<?php

namespace App\Repositories\Interfaces;

use App\User;
use App\Models\Panel;
use App\Models\Group;

interface PanelRepositoryInterface
{
    public function userPanels(User $user, string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $private = false, bool $paginate = true);
    public function groupPanels(User $user, Group $group, string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $private = false, bool $paginate = true);
    public function publicPanels(string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $paginate = true);
    public function publicGroupPanels(Group $group, string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $paginate = true);
    public function destroyPanel(Panel $panel);
    public function duplicate(Panel $panel);
}
