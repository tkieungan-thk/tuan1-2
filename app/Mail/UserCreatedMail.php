<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $resetUrl)
    {
        $this->user = $user;
        $token = Password::createToken($user);
        $this->resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email]));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Created Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user_created',
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
}
