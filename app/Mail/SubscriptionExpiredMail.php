<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Ad;
use App\Models\EmailTemplate;

class SubscriptionExpiredMail extends Mailable
{
    use SerializesModels;

    public $ad;
    protected $emailTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
        
        // Načítame EmailTemplate z databázy
        $this->emailTemplate = EmailTemplate::getByKey('subscription_expired');
        
        // Ak template neexistuje, vytvoríme fallback
        if (!$this->emailTemplate) {
            \Log::warning('EmailTemplate "subscription_expired" not found, using fallback');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Predplatné vypršalo - DiskretneDnes.sk';
        
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
            view: 'emails.subscription-expired',
            with: [
                'ad' => $this->ad,
                'user' => $this->ad->user,
            ],
        );
    }

    /**
     * Pripraví dáta pre template
     */
    private function getTemplateData(): array
    {
        return [
            'ad_id' => $this->ad->id,
            'ad_nickname' => $this->ad->nickname ?? 'Neznámy',
            'ad_views' => number_format($this->ad->views ?? 0),
            'package_price' => '40', // Default price - môže byť dynamické
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