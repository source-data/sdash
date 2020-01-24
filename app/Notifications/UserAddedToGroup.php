<?php

namespace App\Notifications;

use App\User;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserAddedToGroup extends Notification
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
    public function __construct(User $user, Group $group, String $token)
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
                    ->subject('Group Invitation')
                    ->line('You have been invited to join a sharing group.')
                    ->line('If you would like to join the group "' . $this->group->name . '", click below to confirm.')
                    ->action('Join Group', $this->confirmationUrl())
                    ->line('If you do not wish to join the group, feel free to ignore this message');
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
        return URL::signedRoute('group.join',['group' => $this->group->id, 'token' => $this->token]);
    }
}
