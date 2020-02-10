<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user, Request $request)
    {
        $request->validate([
            "name"  =>  ['max:40']
        ]);

        if ($request->query('name')) {

            $userList = User::where(\DB::raw("CONCAT(firstname, ' ', surname)"), 'like', '%' . $request->query('name') . '%')->limit(40)->get();

            return ($userList->isEmpty()) ? API::response(204, "No matching records found", []) : API::response(200, "A list of users", $userList);
        }

        return API::response(200, "A list of all users.", User::all());
    }


    /**
     * Display the specified resource.
     *
     * @param  App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return API::response(200, "User record.", $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, Request $request)
    {
        $loggedInUser = auth()->user();

        if ($loggedInUser->id !== $user->id && !$loggedInUser->is_superadmin()) abort(403, 'Access denied');

        $data = $request->only(["firstname", "surname", "email", "institution_name", "institution_address", "department_name", "linkedin", "twitter", "orcid"]);

        $user->update($data);

        return API::response(200, "User record updated.", $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Request $request)
    {
        if (!auth()->user()->is_superadmin()) abort(403, "Access Denied");

        $name = "{$user->firstname} {$user->surname}";

        $user->delete();

        return API::response(204, $name . " has been deleted.");
    }

    /**
     * Return the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        if (!auth()->user()) abort(404, "User could not be found.");
        return API::response(
            200,
            "Logged-in user.",
            User::where('id', auth()->user()->id)
                ->with(['confirmedGroups' => function ($query) {
                    $query->with(['confirmedUsers' => function ($query) {
                        $query->withPivot(['role']);
                    }]);
                    $query->withCount(['confirmedUsers', 'panels']);
                    $query->withPivot('role');
                }])
                ->first()
        );
    }

    public function removeFromGroup(Group $group)
    {
        $loggedInUser = auth()->user();

        if (Gate::allows('view-group', $group) || Gate::allows('modify-group', $group)) {

            /*
            User should not be removed from group if:
                1. They are group owner
                2. They are last remaining admin
            */

            // 1.
            if ($loggedInUser->id === $group->user_id) return API::response(403, "Group owner cannot leave group.", []);

            // 2.
            if ($group->users()->wherePivot("role", "admin")->count() < 2 && $group->users()->where("user_id", $loggedInUser->id)->wherePivot("role", "admin")->exists()) return API::response(403, "A group must have an administrator", []);

            $group->users()->detach($loggedInUser->id);

            $userPanels = $group->panels()->where("user_id", $loggedInUser->id)->get();

            $panelIdsToRemove = [];

            if (!$userPanels->isEmpty()) {
                foreach ($userPanels as $panel) {
                    $panelIdsToRemove[] = $panel->id;
                }

                $group->panels()->detach($panelIdsToRemove);
            }

            return API::response(200, "{$loggedInUser->firstname} {$loggedInUser->surname} removed from group", []);
        } else {
            return API::response(403, "You are forbidden from removing this user from the group.", []);
        }
    }
}
