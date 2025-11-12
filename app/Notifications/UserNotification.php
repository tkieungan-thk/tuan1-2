<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    protected string $password;

    protected string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $password, string $type = 'created')
    {
        $this->password = $password;
        $this->type     = $type;
    }

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
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->greeting(__('emails.hello') . ' ' . $notifiable->name);

        if ($this->type === 'created') {
            $mail->subject(__('emails.user_created_subject'))
                ->line(__('emails.account_created'))
                ->line(__('emails.login_email') . ': ' . $notifiable->email)
                ->line(__('emails.password') . ': ' . $this->password)
                ->line(__('emails.thank_you'));
        } elseif ($this->type === 'updated') {
            $mail->subject(__('emails.user_updated_subject'))
                ->line(__('emails.account_updated'))
                ->line(__('emails.login_email') . ': ' . $notifiable->email)
                ->line(__('emails.new_password') . ': ' . $this->password)
                ->line(__('emails.thank_you'));
        }

        return $mail;
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
