<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailLog;

class SupportTicketResponse extends Mailable
{
    use SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Odpoveď na váš support ticket #' . $this->ticket->id . ' - ' . $this->ticket->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Skús načítať email template
        $template = EmailTemplate::getByKey('support_ticket_response');
        
        if ($template && $template->is_active) {
            return new Content(
                htmlBody: $template->renderContent($this->getTemplateData()),
            );
        }

        // Fallback na pôvodný view
        return new Content(
            view: 'emails.support-ticket-response',
        );
    }

    /**
     * Get template data for email template
     */
    private function getTemplateData(): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_subject' => $this->ticket->subject,
            'ticket_status' => $this->ticket->status,
            'ticket_created_at' => $this->ticket->created_at->format('d.m.Y H:i'),
            'user_name' => $this->ticket->user->name,
            'user_email' => $this->ticket->user->email,
            'site_name' => config('app.name', 'Erotikon.sk'),
            'support_url' => route('support.ticket.show', $this->ticket),
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
    
    /**
     * Build the message and log it.
     */
    public function build()
    {
        $built = parent::build();
        
        // Log email after building
        $this->logEmail();
        
        return $built;
    }
    
    /**
     * Log this email
     */
    private function logEmail()
    {
        try {
            // Get recipient from 'to' addresses
            $to = $this->to ?? [];
            $recipient = !empty($to) ? $to[0]['address'] : 'unknown';
            
            EmailLog::logEmail(
                $recipient,
                'Odpoveď na váš support ticket #' . $this->ticket->id . ' - ' . $this->ticket->subject,
                null, // Body will be rendered by view
                'support',
                [
                    'ticket_id' => $this->ticket->id,
                    'ticket_subject' => $this->ticket->subject,
                    'mail_class' => self::class
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to log SupportTicketResponse email: ' . $e->getMessage());
        }
    }
}
