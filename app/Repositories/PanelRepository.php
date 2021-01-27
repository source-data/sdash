<?php

namespace App\Repositories;

use App\User;
use App\Models\Panel;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\PanelRepositoryInterface;

class PanelRepository implements PanelRepositoryInterface
{
    /**
     * return all panels accessible to this user, either through group membership, ownership, or if the panel is public
     *
     * @param User $user The logged-in user
     * @param string $search A search string
     * @param Array $tags An array of tags to search
     * @param bool $private Whether the search should only return panels owned by the user
     * @param bool $paginate Whether to return the results in a paginated list or a full list
     * @return void
     */
    public function userPanels(User $user, string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $private = false, bool $paginate = true)
    {

        $panelQuery = Panel::where(
            function ($query) use ($user, $private) {
                $query->where('user_id', $user->id);
                if (!$private) {
                    $query->orWhere('is_public', true)->orWhereHas('groups', function ($query) use ($user) {
                        $query->whereHas('confirmedUsers', function ($query) use ($user) {
                            $query->where('users.id', $user->id);
                        });
                    })->orWhereHas('authors', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
                }
            }
        ); //Grouped query select ... where (x and y and z) and a and b;

        insertQueryConditions($panelQuery, $search, $authors, $tags);

        $panelQuery->with(['groups', 'tags', 'authors', 'externalAuthors']);

        insertOrderByClause($panelQuery, $sortOrder);

        return ($paginate) ? $panelQuery->with('user')->paginate(20) : $panelQuery->with('user')->get();
    }

    public function groupPanels(User $user, Group $group, string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $private = false, bool $paginate = true)
    {
        $panelQuery = $group->panels()->with(['groups', 'tags', 'user', 'authors', 'externalAuthors']);

        // If this is a query for user's own panel, add the limit to query
        if ($private) {
            $panelQuery->where(function ($query) use ($user, $private) {
                $query->where("user_id", $user->id);
            });
        }

        insertQueryConditions($panelQuery, $search, $authors, $tags);

        insertOrderByClause($panelQuery, $sortOrder);

        return ($paginate) ? $panelQuery->with('user')->paginate(20) : ["data" => $panelQuery->with('user')->get()];
    }

    public function publicPanels(string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $paginate = true)
    {
        $panelQuery = Panel::where('is_public', true);

        insertQueryConditions($panelQuery, $search, $authors, $tags);

        if (env("APP_ENV") === "local") {
            $panelQuery->with(['confirmedGroups', 'tags']);
        }

        insertOrderByClause($panelQuery, $sortOrder);

        return ($paginate) ? $panelQuery->with('user')->paginate(20) : $panelQuery->with('user')->get();
    }

    public function destroyPanel(Panel $panel)
    {
        Storage::deleteDirectory($panel->id);

        $panel->comments()->delete();
        $panel->figures()->detach();
        $panel->groups()->detach();
        $panel->files()->delete();
        $panel->image()->delete();
        $panel->tags()->detach();
        $panel->delete();

        return true;
    }
}

function insertQueryConditions(&$panelQuery, $search, $authors, $tags)
{
    // If there's a search string, add it to the where clause
    if (isset($search)) $panelQuery->where(function ($query) use ($search) {
        $query
            ->where("title", "like", "%{$search}%")
            ->orWhere("caption", "like", "%{$search}%")
            ->orWhereHas("tags", function ($query) use ($search) {
                $query->where("content", "like", "%{$search}%");
            })
            ->orWhereHas("authors", function ($query) use ($search) {
                $query->where(DB::raw("concat(firstname, ' ', surname)"), "like", "%{$search}%");
            })
            ->orWhereHas("externalAuthors", function ($query) use ($search) {
                $query->where(DB::raw("concat(firstname, ' ', surname)"), "like", "%{$search}%");
            });
    });

    // Filter by authors
    if (isset($authors)) $panelQuery->where(function ($query) use ($authors) {
        $query->whereHas("authors", function ($query) use ($authors) {
            $query->select(DB::raw('count(distinct panel_user.user_id)'))->whereIn("panel_user.user_id", $authors);
        }, '=', count($authors));
    });

    // Filter by keywords
    if (isset($tags)) $panelQuery->where(function ($query) use ($tags) {
        $query->whereHas("tags", function ($query) use ($tags) {
            $query->select(DB::raw('count(distinct tags.id)'))->whereIn("tags.id", $tags);
        }, '=', count($tags));
    });
}

function insertOrderByClause(&$panelQuery, $sortOrder)
{
    $useAscendingOrder = (substr($sortOrder, -4) === '-asc');
    switch ($sortOrder) {
        case 'title-asc':
        case 'title-desc':
            $panelQuery->orderByTitle($useAscendingOrder);
            break;
        case 'creation-date-asc':
        case 'creation-date-desc':
            $panelQuery->orderByCreated($useAscendingOrder);
            break;
        default:
            $panelQuery->orderByUpdated($useAscendingOrder);
    }
}
