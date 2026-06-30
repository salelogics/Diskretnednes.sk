<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use SerializesModels;

    public $contactData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $contactData)
    {
        // Debug: log what data we received
        \Log::info('ContactFormMail: Data received', [
            'contactData' => $contactData
        ]);
        
        $this->contactData = $contactData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Debug: Skontrolujme čo máme v contactData
        \Log::info('ContactFormMail envelope: Checking contactData', [
            'contactData' => $this->contactData,
            'name' => $this->contactData['name'] ?? 'MISSING',
            'email' => $this->contactData['email'] ?? 'MISSING',
            'subject' => $this->contactData['subject'] ?? 'MISSING'
        ]);

        return new Envelope(
            subject: 'Nová správa z kontaktného formulára - ' . ($this->contactData['subject'] ?? 'Bez predmetu'),
            // OPRAVENÉ: replyTo sa má uviesť len email adresa, nie asociatívne pole
            replyTo: $this->contactData['email'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            with: [
                'name' => $this->contactData['name'],
                'email' => $this->contactData['email'],
                'subject' => $this->contactData['subject'],
                'messageContent' => $this->contactData['message'],
                'submittedAt' => now()->format('d.m.Y H:i'),
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