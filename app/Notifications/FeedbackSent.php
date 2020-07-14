<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeedbackSent extends Notification
{
    use Queueable;

    protected $user;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, String $message)
    {
        $this->user     = $user;
        $this->message  = $message;
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
            ->subject("New Feedback on SDash")
            ->greeting("{$this->user->firstname} {$this->user->surname} ({$this->user->email}) has sent you feedback")
            ->line($this->message);
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
