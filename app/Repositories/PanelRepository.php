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
    protected $defaultPageSize = 20;

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

        $panelQuery = userPanelsQuery($user, $private);

        insertQueryConditions($panelQuery, $search, $authors, $tags);

        $panelQuery->with(['groups', 'tags', 'authors', 'externalAuthors']);

        insertOrderByClause($panelQuery, $sortOrder);

        return ($paginate) ? $panelQuery->with('user')->paginate($this->defaultPageSize) : $panelQuery->with('user')->get();
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

        return ($paginate) ? $panelQuery->with('user')->paginate($this->defaultPageSize) : ["data" => $panelQuery->with('user')->get()];
    }

    public function publicPanels(string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $paginate = true)
    {
        $panelQuery = Panel::where('is_public', true);

        loadRelatedModels($panelQuery);

        insertQueryConditions($panelQuery, $search, $authors, $tags);

        insertOrderByClause($panelQuery, $sortOrder);

        $panels = ($paginate) ? $panelQuery->paginate($this->defaultPageSize) : $panelQuery->get();

        foreach ($panels as $i => $panel) {
            foreach ($panel['authors'] as $j => $author) {
                if ($author['author_role']['role'] !== User::PANEL_ROLE_CORRESPONDING_AUTHOR) {
                    unset($panels[$i]['authors'][$j]['email']);
                }
            }
        }

        return $panels;
    }

    public function publicGroupPanels(Group $group, string $search = null, array $tags = null, array $authors = null, string $sortOrder = null, bool $paginate = true)
    {
        $panelQuery = $group->panels();

        loadRelatedModels($panelQuery);

        $panelQuery->where("is_public", true);

        insertQueryConditions($panelQuery, $search, $authors, $tags);

        insertOrderByClause($panelQuery, $sortOrder);

        return ($paginate) ? $panelQuery->paginate($this->defaultPageSize) : $panelQuery->get();
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

    /**
     * Find the page that the panel is on.
     * 
     * Returns the 1-based index of the page that the panel is on, if the given user is allowed to view the panel.
     * If the user is not allowed to view the panel `null` is returned.
     * 
     * Assumes default ordering of the panels.
     * 
     * @param Panel $panel The panel whose page should be returned.
     * @param User $user The user for whose view the page should be returned. If `null` is given, an anonymous user is presumed.
     * @return int The page that the given panel is on.
     */
    public function pageOfPanel(Panel $panel, User $user = null)
    {
        if (is_null($user)) {
            $panelQuery = Panel::where('is_public', true);
        } else {
            $panelQuery = userPanelsQuery($user, false);
        }
        $sortOrder = null;
        insertOrderByClause($panelQuery, $sortOrder);
        $userPanels = $panelQuery->select('id')->get()->map(function ($item, $key) {
            return $item['id'];
        });

        $indexOfPanel = array_search($panel->id, $userPanels->toArray());
        if ($indexOfPanel === false) {
            // panel is not in user panels
            return null;
        }
        $indexOfPage = intval(floor($indexOfPanel / $this->defaultPageSize)) + 1;
        return $indexOfPage;
    }
}

function userPanelsQuery(User $user, bool $private)
{
    return Panel::where(
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
    );
}

function loadRelatedModels(&$panelQuery)
{
    $panelQuery->with([
        'user' => function ($query) {
            $query->select(["id", "firstname", "surname", "role", "department_name", "institution_name", "orcid"]);
        },
        'groups' => function ($query) {
            $query->where('is_public', true);
        },
        'tags',
        'authors'  => function ($query) {
            $query->select(["users.id", "firstname", "surname", "department_name", "institution_name", "orcid", "email"]);
        },
        'externalAuthors'   => function ($query) {
            $query->select(["external_authors.id", "firstname", "surname", "department_name", "institution_name", "orcid"]);
        }
    ]);
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
    if (isset($authors)) {
        $authors = resolveUserIds($authors);
        $panelQuery->where(function ($query) use ($authors) {
            $query->whereHas("authors", function ($query) use ($authors) {
                $query->select(DB::raw('count(distinct panel_user.user_id)'))->whereIn("panel_user.user_id", $authors);
            }, '=', count($authors));
        });
    }

    // Filter by keywords
    if (isset($tags)) {
        $panelQuery->where(function ($query) use ($tags) {
            $query->whereHas("tags", function ($query) use ($tags) {
                $query->select(DB::raw('count(distinct tags.id)'))->whereIn("tags.id", $tags);
            }, '=', count($tags));
        });
    }
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
            $panelQuery->orderByUpdated($useAscendingOrder)->orderBy('id');
    }
}

function resolveUserIds(array $ids)
{
    foreach ($ids as $i => $id) {
        $id = strval($id);
        if (!is_numeric($id) && preg_match(User::ORCID_REGEX, $id)) {
            $user = User::select(["id"])->where("orcid", '=', $id)->first();
            $id = $user ? $user->id : 0;
        } else {
            $id = ctype_digit($id) ? intval($id) : 0;
        }
        $ids[$i] = $id;
    }
    return array_unique($ids);
}
