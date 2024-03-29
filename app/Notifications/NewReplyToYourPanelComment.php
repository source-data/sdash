<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Panel;
use App\Models\Comment;
use App\Notifications\PanelUrls;
use App\User;

class NewReplyToYourPanelComment extends Notification
{
    use Queueable, PanelUrls;

    protected $user;
    protected $comment;

    /**
     * create a new notification of a comment
     *
     * @param User the user who submitted the comment
     * @param Panel the panel which was commented on
     * @param Comment the comment which was posted
     */
    public function __construct(User $user, Panel $panel, Comment $comment)
    {
        $this->user = $user;
        $this->panel = $panel;
        $this->comment = $comment;
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
            ->subject('New Reply to Your Comment on SDASH')
            ->greeting("New Reply Notification")
            ->line("{$this->user->firstname} {$this->user->surname} has replied to your comment on the SmartFigure \"{$this->panel->title}\"")
            ->line('Their reply says:')
            ->line($this->comment->comment)
            ->action('View SmartFigure', $this->panelDetailUrl());
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
