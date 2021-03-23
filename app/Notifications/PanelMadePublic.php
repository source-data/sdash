<?php

namespace App\Notifications;

use App\User;
use App\Models\Panel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PanelMadePublic extends Notification
{
    use Queueable;

    private $user;
    private $author;
    private $panel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $author, Panel $panel)
    {
        $this->user = $user;
        $this->author = $author;
        $this->panel = $panel;
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
            ->subject('Your Panel Has Been Made Public')
            ->greeting("Your panel has been made public.")
            ->line("{$this->user->firstname} {$this->user->surname} made the panel \"{$this->panel->title}\" publicly available on SDash.")
            ->line("You will see the panel on the public dashboard or by clicking on the button below.")
            ->action('View Panel', $this->panelUrl());
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

    protected function panelUrl()
    {
        return url('/panel/' . $this->panel->id);
    }
}
