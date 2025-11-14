<?php

namespace App\Notifications;

use App\Enums\UserNotificationType;
use App\Mail\UserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private string $password,
        private UserNotificationType $type = UserNotificationType::CREATED
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return UserMail
     */
    public function toMail(object $notifiable)
    {
        return (new UserMail(
            password: $this->password,
            type: $this->type,
            user: $notifiable,
        ))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
