<?php

namespace App\Mail;

use App\Enums\UserNotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $password, public UserNotificationType $type, public object $user)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user',
            with: [
                'user'     => $this->user,
                'password' => $this->password,
                'type'     => $this->type,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Lấy tiêu đề mail
     *
     * @return array|string|null
     */
    public function getSubject(): string
    {
        return match ($this->type) {
            UserNotificationType::CREATED => __('emails.user_created_subject'),
            UserNotificationType::UPDATED => __('emails.user_updated_subject'),
        };
    }
}
