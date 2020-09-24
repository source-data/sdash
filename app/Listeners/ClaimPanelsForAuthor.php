<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ExternalAuthor;
use App\Models\Author;
use App\Models\Panel;
use App\User;


class ClaimPanelsForAuthor
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {

        $this->allocateAuthorPanels($event->user);
    }


    protected function allocateAuthorPanels(User $user)
    {
        // see see https://laravel.com/docs/7.x/verification#after-verifying-emails
        $panels = Panel::with('externalAuthors')->whereHas('externalAuthors', function ($query) use ($user) {
            return $query->where('email', $user->email);
        })->get();

        foreach ($panels as $panel) {

            $externalRecords = $panel->externalAuthors->filter(function ($externalAuthor) use ($user) {
                return $externalAuthor->email === $user->email;
            });

            foreach ($externalRecords as $externalAuthor) {
                $panel->authors()->attach($user, ['role' => $externalAuthor->author_role->role, 'order' => $externalAuthor->author_role->order]);

                $externalAuthor->delete();
            }
        }
    }
}
