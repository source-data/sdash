<?php

namespace App\Notifications;

use App\User;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAppliedToJoinGroup extends Notification
{
    use Queueable;

    protected $user;
    protected $group;
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Group $group, string $token)
    {
        $this->user = $user;
        $this->group = $group;
        $this->token = $token;
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
            ->subject('A user has applied to join your group: ' . $this->group->name)
            ->line('The following user has applied to join your group: ' . $this->group->name)
            ->line('Name: ' . $this->user->firstname . ' ' . $this->user->surname)
            ->line('Organisation: ' . $this->user->department_name . ', ' . $this->user->institution_name)
            ->action('Accept user', $this->confirmationUrl());
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

    protected function confirmationUrl()
    {
        return URL::signedRoute('group.accept', ['group' => $this->group->id, 'token' => $this->token]);
    }
}
