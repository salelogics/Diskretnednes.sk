<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\EmailTemplate;

class WelcomeMail extends Mailable
{
    use SerializesModels;

    public $user;
    protected $emailTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        
        // Načítame EmailTemplate z databázy
        $this->emailTemplate = EmailTemplate::getByKey('welcome');
        
        // Ak template neexistuje, vytvoríme fallback
        if (!$this->emailTemplate) {
            \Log::warning('EmailTemplate "welcome" not found, using fallback');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Vitajte na DiskretneDnes.sk - Váš účet bol vytvorený';
        
        if ($this->emailTemplate) {
            $data = $this->getTemplateData();
            $subject = $this->emailTemplate->renderSubject($data);
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->emailTemplate) {
            // Používame EmailTemplate z administrácie
            $data = $this->getTemplateData();
            $htmlContent = $this->emailTemplate->renderContent($data);
            
            return new Content(
                html: $htmlContent,
            );
        }
        
        // Fallback na pôvodný view ak template neexistuje
        return new Content(
            view: 'emails.welcome',
            with: [
                'user' => $this->user,
            ],
        );
    }

    /**
     * Pripraví dáta pre template
     */
    private function getTemplateData(): array
    {
        return [
            'user_name' => $this->user->name ?? 'Používateľ',
            'user_email' => $this->user->email,
        ];
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