<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Group;
use App\Models\UserConsent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publicIndex(User $user, Request $request)
    {
        $request->validate([
            "name"  =>  ['max:40']
        ]);

        if ($request->query('name')) {

            $userList = User::select(["id", "firstname", "surname", "institution_name"])
                ->whereHas('panels', function ($query) {
                    $query->where('is_public', true);
                })
                ->where(\DB::raw("CONCAT(firstname, ' ', surname)"), 'like', '%' . $request->query('name') . '%')
                ->limit(40)->get();

            return ($userList->isEmpty()) ? API::response(204, "No matching records found", []) : API::response(200, "A list of users", $userList);
        }
        return API::response(204, "No matching records found", []);
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

        $this->validate($request, [
            'firstname' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'orcid' => ['nullable', 'regex:/0000-000(1-[5-9]|2-[0-9]|3-[0-4])\d{3}-\d{3}[\dX]/i'],
        ]);

        $user->update($data);

        return API::response(200, "User record updated.", $user);
    }

    public function updateConsent(User $user, Request $request)
    {
        $loggedInUser = auth()->user();

        if ($loggedInUser->id !== $user->id) {
            abort(403, 'Access denied');
        }

        $hasConsented = $request->boolean('has_consented');

        if (!$hasConsented) {
            abort(403, 'Consent withdrawal is not allowed');
        }

        $user->update(['has_consented' => $hasConsented]);

        if ($hasConsented) {
            UserConsent::create([
                'user_id' => $user->id,
            ]);
        }

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

        $user = User::where('id', auth()->user()->id)
            ->with(['groups' => function ($query) {
                $query->with(['confirmedUsers' => function ($query) {
                    $query->withPivot(['role']);
                }]);
                $query->withCount(['confirmedUsers', 'requestedUsers', 'panels']);
                $query->withPivot(['role', 'token', 'status']);
            }])
            ->first();

        return API::response(
            200,
            "Logged-in user.",
            $user
        );
    }

    public function removeFromGroup(Group $group)
    {
        $loggedInUser = auth()->user();

        if (Gate::allows('view-group', $group) || Gate::allows('modify-group', $group)) {

            /*
            User should not be removed from group if:
                They are last remaining admin
            */
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

    /**
     * Allow logged-in user to modify their own password
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(User $user, Request $request)
    {
        $request->validate([
            'existingPassword'  => ['required', 'password'],
            'newPassword1'      => ['required', 'same:newPassword2', 'min:8'],
            'newPassword2'      => ['required', 'same:newPassword1', 'min:8']
        ]);

        $loggedInUser = auth()->user();

        if (!$loggedInUser->id === $user->id) return API::response(403, "You cannot change another user's password.", ["success" => false]);

        $user->password = Hash::make($request->input('newPassword1'));
        $user->save();

        return API::response(200, "Password changed", ["success" => true]);

        return $request;
    }

    /**
     * Change user avatar
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changeAvatar(User $user, Request $request)
    {
        $loggedInUser = auth()->user();

        if (($loggedInUser->id !== $user->id)
            && !$loggedInUser->is_superadmin()) {
            abort(403, 'Access denied');
        }

        if (!$request->file('avatar')) {
            return API::response(400, "No avatar uploaded", []);
        }

        // Delete existing avatar
        if ($user->avatar !== null) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        // Process the uploaded image
        $filename = Str::random(20) . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->storeAs('avatars/', $filename, 'public');

        // Update the user avatar
        $user->avatar = $filename;

        if ($user->save()) {
            return API::response(200, "Avatar changed", [
                "avatar" => $filename,
            ]);
        } else {
            return API::response(500, "Failed to change avatar", []);
        }
    }

    /**
     * Delete user avatar
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAvatar(User $user, Request $request)
    {
        $loggedInUser = auth()->user();

        if (($loggedInUser->id !== $user->id)
            && !$loggedInUser->is_superadmin()) {
            abort(403, 'Access denied');
        }

        $user->avatar = null;

        if ($user->save()) {
            return API::response(200, "Avatar deleted", []);
        } else {
            return API::response(500, "Failed to delete avatar", []);
        }
    }
}
