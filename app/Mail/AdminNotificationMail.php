<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use SerializesModels;

    public $title;
    public $message;
    public $actionUrl;
    public $actionText;
    public $type;
    public $priority;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $message, string $type = 'info', string $priority = 'normal', ?string $actionUrl = null, ?string $actionText = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->priority = $priority;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $priorityPrefix = $this->priority === 'urgent' ? '[URGENTNÉ] ' : 
                         ($this->priority === 'high' ? '[VYSOKÁ PRIORITA] ' : '');

        return new Envelope(
            subject: $priorityPrefix . $this->title . ' - DiskretneDnes.sk Admin',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-notification',
            with: [
                'title' => $this->title,
                'content' => $this->message,  // Zmenil som z 'message' na 'content'
                'type' => $this->type,
                'priority' => $this->priority,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
            ],
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
