<?php

namespace App\Repositories;

use App\User;
use App\Models\Panel;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\PanelRepositoryInterface;

class PanelRepository implements PanelRepositoryInterface
{

/**
 * return all panels accessible to this user, either through group membership, ownership, or if the panel is public
 *
 * @param User $user The logged-in user
 * @param string $search A search string
 * @param Array $tags An array of tags to search
 * @param boolean $private Whether the search should only return panels owned by the user
 * @return void
 */
    public function userPanels(User $user, string $search=null, Array $tags=null, bool $private = false, bool $paginate=true)
    {
        // $groupAccessiblePanelIdsQuery   = User::where('id',$user->id)->with('groups.panels');
        // $groupAccessiblePanelIds        = $groupAccessiblePanelIdsQuery->get()->pluck("groups.*.panels.*.id")->flatten()->unique();

        $panelQuery = Panel::where(
            function($query) use($user, $private){
                $query->where('user_id', $user->id);
                if(!$private){
                    $query->orWhereNotNull('made_public_at')->orWhereHas('groups', function($query) use($user) {
                        $query->whereHas('confirmedUsers', function($query) use($user) {
                            $query->where('users.id', $user->id);
                        });
                    });

                }
            }
        ); //Grouped query select ... where (x and y and z) and a and b;


        // If there's a search string, add it to the where clause
        if(isset($search)) $panelQuery->where(function($query) use($search) {
            $query
                ->where("title","like","%{$search}%")
                ->orWhere("caption","like","%{$search}%")
                ->orWhereHas("tags", function($query) use($search){
                    $query->where("content", "like", "%{$search}%");
                });
        });

        //if there's a tag search, add it to the where clause
        if(isset($tags)) $panelQuery->where(function($query) use($tags) {
            $query->whereHas("tags", function($query) use($tags) {
                $query->whereIn("content",$tags);
            });
        });

        $panelQuery->with(['groups','tags']);

        //add order by clause
        $panelQuery->orderByUpdated();

        return ($paginate) ? $panelQuery->with('user')->paginate(20) : $panelQuery->with('user')->get();
    }


    public function GroupPanels(User $user, Group $group, string $search=null, Array $tags=null, bool $private = false, bool $paginate=true)
    {
        $panelQuery = $group->panels()->with(['groups', 'tags', 'user']);

        // If this is a query for user's own panel, add the limit to query
        if($private){
            $panelQuery->where(function($query) use($user, $private) {
                $query->where("user_id", $user->id);
            });

        }

        // If there's a search string, add it to the where clause
        if(isset($search)) $panelQuery->where(function($query) use($search) {
            $query
                ->where("title","like","%{$search}%")
                ->orWhere("caption","like","%{$search}%")
                ->orWhereHas("tags", function($query) use($search){
                    $query->where("content", "like", "%{$search}%");
                });
        });

        //if there's a tag search, add it to the where clause
        if(isset($tags)) $panelQuery->where(function($query) use($tags) {
            $query->whereHas("tags", function($query) use($tags) {
                $query->whereIn("content",$tags);
            });
        });

        //add order by clause
        $panelQuery->orderByUpdated();

        return ($paginate) ? $panelQuery->with('user')->paginate(20) : $panelQuery->with('user')->get();

    }


    public function publicPanels(string $search=null, Array $tags=null, bool $paginate=true)
    {
        $panelQuery = Panel::whereNotNull('made_public_at');

        // If there's a search string, add it to the where clause
        if(isset($search)) $panelQuery->where(function($query) use($search) {
            $query
                ->where("title","like","%{$search}%")
                ->orWhere("caption","like","%{$search}%")
                ->orWhereHas("tags", function($query) use($search){
                    $query->where("content", "like", "%{$search}%");
                });
        });

        //if there's a tag search, add it to the where clause
        if(isset($tags)) $panelQuery->where(function($query) use($tags) {
            $query->whereHas("tags", function($query) use($tags) {
                $query->whereIn("content",$tags);
            });
        });

        if(env("APP_ENV")==="local") {
            $panelQuery->with(['confirmedGroups','tags']);
        }

        //add order by clause
        $panelQuery->orderByUpdated();


        return ($paginate) ? $panelQuery->with('user')->paginate(20) : $panelQuery->with('user')->get();

    }


}