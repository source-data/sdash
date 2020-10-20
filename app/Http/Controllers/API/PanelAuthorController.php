<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Panel;
use Illuminate\Http\Request;
use App\Models\ExternalAuthor;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Notifications\UserAddedAsPanelAuthor;

class PanelAuthorController extends Controller
{
    /**
     * Update the author details attached to a panel.
     *
     * @param Request $request
     * @param Panel $panel
     * @return void
     */
    public function update(Request $request, Panel $panel)
    {
        // the array or authors is required and must have an author_role and order and origin
        $request->validate([
            'authors' => ['required', 'array'],
            'authors.*.author_role' => ['required', Rule::in([User::PANEL_ROLE_AUTHOR, User::PANEL_ROLE_CORRESPONDING_AUTHOR, User::PANEL_ROLE_CURATOR])],
            'authors.*.order'   => ['required'],
            'authors.*.origin'  => ['required', Rule::in(["users", "external"])]
        ]);

        // check that authenticated user has edit permission on this panel
        if (!Gate::allows('modify-panel', $panel)) return API::response(401, "Permission to edit was denied.", []);

        $user = auth()->user();
        $panelOwner = $panel->user;
        $newAuthors = $request->input("authors");
        $userAuthors = array_filter($newAuthors, function ($author) {
            return $author["origin"] === 'users';
        });
        $externalAuthors = array_filter($newAuthors, function ($author) {
            return $author["origin"] === 'external';
        });
        $existingExternalAuthors = $panel->externalAuthors()->get();
        $existingUserAuthors = $panel->authors()->get();

        // There must be at least one corresponding author in the list
        if (!$this->mustBeAtLeastOneCorrspondingAuthor($newAuthors)) return API::response(400, "Corresponding author is required.", []);

        // The submitted author list must have no repeated values in the order field
        if (!$this->authorOrderUnique($newAuthors)) return API::response(400, "Author order must have no repeated values.", []);

        // The order field must be sequential and start from zero
        if (!$this->authorOrderSequential($newAuthors)) return API::response(400, "Author order must be sequential from zero.", []);

        //check that panel owner is not removed
        $containsOwner = array_filter($userAuthors, function ($author) use ($panelOwner) {
            return (isset($author["id"]) && $author["id"] === $panelOwner->id);
        });
        if (!$containsOwner) return API::response(400, "Panel owner cannot be removed.", []);
        unset($containsOwner);

        // attach new user authors
        foreach ($userAuthors as $author) {

            $existingUserAuthor = $panel->authors()->where('users.id', $author["id"])->first();

            // if author is already attached to panel, just update role
            if (!empty($existingUserAuthor)) {
                $panel->authors()->updateExistingPivot($existingUserAuthor->id, [
                    'role' => $author["author_role"],
                    'order' => $author["order"]
                ]);
            } else {
                // attach existing user as an author AND notify them
                $newUserAuthor = User::find($author["id"]);
                $panel->authors()->attach($newUserAuthor->id, [
                    'role' => $author["author_role"],
                    'order' => $author["order"]
                ]);
                $newUserAuthor->notify(new UserAddedAsPanelAuthor($user, $newUserAuthor, $panel, $author["author_role"]));
            }
        }

        // if existing author is not included in the submitted dataset, detach them
        foreach ($existingUserAuthors as $userAuthor) {
            if (count(array_filter($userAuthors, function ($submittedAuthor) use ($userAuthor) {
                return ($submittedAuthor["id"] === $userAuthor->id);
            })) === 0) {
                $panel->authors()->detach($userAuthor->id);
            }
        }

        // attach new external authors ()
        foreach ($externalAuthors as $author) {
            // if author already exists, reattach them
            if (isset($author["id"])) {
                $existingExternalAuthor = $panel->externalAuthors()->where('external_authors.id', $author["id"])->first();

                if (!empty($existingExternalAuthor)) {
                    $existingExternalAuthor->firstname = $author["firstname"];
                    $existingExternalAuthor->surname = $author["surname"];
                    $existingExternalAuthor->email = $author["email"];
                    $existingExternalAuthor->department_name = $author["department_name"];
                    $existingExternalAuthor->institution_name = $author["institution_name"];
                    $existingExternalAuthor->orcid = $author["orcid"];
                    $existingExternalAuthor->save();
                    $panel->externalAuthors()->updateExistingPivot($existingExternalAuthor->id, [
                        'role' => $author["author_role"],
                        'order' => $author["order"]
                    ]);
                }
            } else {
                // otherwise, create them
                $externalAuthorObject = ExternalAuthor::create([
                    'firstname' => $author["firstname"],
                    'surname' => $author["surname"],
                    'institution_name' => $author["institution_name"],
                    'department_name' => $author["department_name"],
                    'orcid' => $author["orcid"],
                    'email' => $author["email"]
                ]);


                $panel->externalAuthors()->attach($externalAuthorObject->id, ["role" => $author["author_role"], "order" => $author["order"]]);
            }
        }

        // delete any external authors that are not part of the submitted dataset
        foreach ($existingExternalAuthors as $author) {
            if (count(array_filter($externalAuthors, function ($submittedAuthor) use ($author) {
                return (isset($submittedAuthor["id"]) && $submittedAuthor["id"] === $author->id);
            })) === 0) {
                $panel->externalAuthors()->where('external_authors.id', $author->id)->delete();
            }
        }

        return API::response(200, "Author list updated.", ["authors" => $panel->authors()->get(), "external_authors" => $panel->externalAuthors()->get()]);
    }

    /**
     * Accepts an array of author data and checks that there are no repeated
     * "order" values as authors cannot share a single position in the
     * author list.
     *
     * @param array $authors
     * @return void
     */
    protected function authorOrderUnique(array $authors)
    {
        $orderedAuthors = [];
        for ($i = 0; $i < count($authors); $i++) {
            $orderedAuthors[$authors[$i]["order"]] = $authors[$i];
        }
        return (count($orderedAuthors) === count($authors));
    }

    /**
     * Checks that the order assigned to each item in an array of authors
     * is strictly sequential. e.g. 0,1,2,3 - no missing values.
     *
     * @param array $authors
     * @return void
     */
    public function authorOrderSequential(array $authors)
    {
        usort($authors, function ($a, $b) {
            if ($a["order"] === $b["order"]) return 0;
            return ($a["order"] < $b["order"]) ? -1 : 1;
        });

        for ($i = 0; $i < count($authors); $i++) {
            if ($authors[$i]["order"] !== $i) return false;
        }
        return true;
    }


    public function mustBeAtLeastOneCorrspondingAuthor(array $authors)
    {
        $correspondingAuthorCount = 0;
        for ($i = 0; $i < count($authors); $i++) {
            if ($authors[$i]["author_role"] === User::PANEL_ROLE_CORRESPONDING_AUTHOR) $correspondingAuthorCount++;
        }
        return ($correspondingAuthorCount > 0);
    }
}
