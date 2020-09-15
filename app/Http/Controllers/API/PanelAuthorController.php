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

class PanelAuthorController extends Controller
{
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

        // remove existing authors
        $panel->authors()->detach();
        $panel->externalAuthors()->detach();

        // attach new user authors
        foreach ($userAuthors as $author) {
            $panel->authors()->attach($author["id"], ["role" => $author["author_role"], "order" => $author["order"]]);
        }

        // attach new external authors
        foreach ($externalAuthors as $author) {
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

        return API::response(200, "Author list updated.", ["authors" => $panel->authors()->get(), "external_authors" => $panel->externalAuthors()->get()]);

        /**
         * Todo:
         *
         * if the submitted list of authors is empty, throw exception - cannot remove all authors
         *
         * The panel owner (uploader) cannot be removed
         *
         * Get existing panel authors
         *
         * 1. if any existing authors are not in the submitted list, detach them
         * 2. if any submitted authors are not in the existing list, attach them
         * 3. update roles and order of all submissions
         * 4. order cannot be repeated
         * 5. order must be sequential
         *
         */
        return API::response(200, "Test Complete", ["woo" => "yeah"]);
    }

    protected function authorOrderUnique(array $authors)
    {
        $orderedAuthors = [];
        for ($i = 0; $i < count($authors); $i++) {
            $orderedAuthors[$authors[$i]["order"]] = $authors[$i];
        }
        return (count($orderedAuthors) === count($authors));
    }

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
}
