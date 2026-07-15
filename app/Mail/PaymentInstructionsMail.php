<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AdPayment;
use App\Models\EmailTemplate;

class PaymentInstructionsMail extends Mailable
{
    use SerializesModels;

    public $payment;
    protected $emailTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct(AdPayment $payment)
    {
        $this->payment = $payment;
        
        // Načítame EmailTemplate z databázy
        $this->emailTemplate = EmailTemplate::getByKey('payment_instructions');
        
        // Ak template neexistuje, vytvoríme fallback
        if (!$this->emailTemplate) {
            \Log::warning('EmailTemplate "payment_instructions" not found, using fallback');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Pokyny na platbu - DiskretneDnes.sk';
        
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
            view: 'emails.payment-instructions',
            with: [
                'payment' => $this->payment,
                'user' => $this->payment->user,
            ],
        );
    }

    /**
     * Pripraví dáta pre template
     */
    private function getTemplateData(): array
    {
        return [
            'user_name' => $this->payment->user->name ?? 'Používateľ',
            'payment_id' => $this->payment->id,
            'amount' => number_format($this->payment->amount, 2),
            'due_date' => $this->payment->created_at ? $this->payment->created_at->addDays(7)->format('d.m.Y') : 'Neznámy',
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