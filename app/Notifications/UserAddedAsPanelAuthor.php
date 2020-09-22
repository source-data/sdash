<?php

namespace App\Notifications;

use App\User;
use App\Models\Panel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAddedAsPanelAuthor extends Notification
{
    use Queueable;

    private $user;
    private $author;
    private $panel;
    private $role;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, User $author, Panel $panel, String $role)
    {
        $this->user = $user;
        $this->author = $author;
        $this->panel = $panel;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You Have Been Added As An Author')
            ->greeting("You have been added as a panel author.")
            ->line("{$this->user->firstname} {$this->user->surname} has given you an author credit on the panel \"{$this->panel->title}\".")
            ->line("You have been given the role: {$this->role}.")
            ->line("You will see the panel on your dashboard next time you log in.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
