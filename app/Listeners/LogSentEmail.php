<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        Log::info('LogSentEmail listener called', ['event_class' => get_class($event)]); // Debug output
        
        try {
            // Získanie základných údajov z emailu - kompatibilita s rôznymi verziami
            $message = $event->sent->getOriginalMessage();
            
            Log::info('LogSentEmail: Message obtained', ['message_class' => get_class($message)]);
            
            // Získanie príjemcov - kompatibilita s rôznymi API
            $recipients = [];
            
            // Skúsime nový spôsob (Symfony 6+)
            if (method_exists($message, 'getTo') && $message->getTo()) {
                foreach ($message->getTo() as $address) {
                    if (is_object($address) && method_exists($address, 'getAddress')) {
                        $recipients[] = $address->getAddress();
                    } elseif (is_string($address)) {
                        $recipients[] = $address;
                    }
                }
            }
            
            // Fallback na starší spôsob
            if (empty($recipients) && method_exists($message, 'getHeaders')) {
                $headers = $message->getHeaders();
                if ($headers->has('to')) {
                    $toHeader = $headers->get('to');
                    if (method_exists($toHeader, 'getAddresses')) {
                        foreach ($toHeader->getAddresses() as $address) {
                            if (is_object($address) && method_exists($address, 'getAddress')) {
                                $recipients[] = $address->getAddress();
                            } elseif (is_string($address)) {
                                $recipients[] = $address;
                            }
                        }
                    }
                }
            }
            
            Log::info('LogSentEmail: Recipients found', ['recipients' => $recipients]);
            
            // Ak nie sú žiadni príjemcovia, nedá sa logovať
            if (empty($recipients)) {
                Log::warning('LogSentEmail: No recipients found, cannot log email');
                return;
            }
            
            // Získanie odosielateľa
            $from = null;
            if (method_exists($message, 'getFrom') && $message->getFrom()) {
                $fromAddresses = $message->getFrom();
                if (is_array($fromAddresses) && !empty($fromAddresses)) {
                    $firstFrom = reset($fromAddresses);
                    if (is_object($firstFrom) && method_exists($firstFrom, 'getAddress')) {
                        $from = $firstFrom->getAddress();
                    } elseif (is_string($firstFrom)) {
                        $from = $firstFrom;
                    }
                }
            }
            
            // Fallback na config ak from nie je dostupný
            if (empty($from)) {
                $from = config('mail.from.address', 'noreply@erotikon.sk');
            }
            
            // Získanie predmetu
            $subject = 'Bez predmetu';
            if (method_exists($message, 'getSubject') && $message->getSubject()) {
                $subject = $message->getSubject();
            }
            
            // Získanie obsahu
            $body = null;
            if (method_exists($message, 'getHtmlBody') && $message->getHtmlBody()) {
                $body = $message->getHtmlBody();
            } elseif (method_exists($message, 'getTextBody') && $message->getTextBody()) {
                $body = $message->getTextBody();
            }
            
            // Získanie typu emailu z predmetu alebo obsahu
            $type = $this->determineEmailType($subject, $body);
            
            Log::info('LogSentEmail: Email details', [
                'from' => $from,
                'subject' => $subject,
                'type' => $type,
                'recipients_count' => count($recipients),
                'has_body' => !empty($body)
            ]);
            
            // Uloženie emailu pre každého príjemcu
            foreach ($recipients as $recipient) {
                EmailLog::create([
                    'to_email' => $recipient,
                    'from_email' => $from,
                    'subject' => $subject,
                    'body' => $body,
                    'type' => $type,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'headers' => $this->getHeaders($message),
                    'metadata' => [
                        'mailer' => config('mail.default'),
                        'queue' => config('queue.default'),
                        'logged_at' => now()->toISOString()
                    ]
                ]);
                
                Log::info('LogSentEmail: Email logged successfully', [
                    'recipient' => $recipient,
                    'subject' => $subject,
                    'type' => $type
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('LogSentEmail: Error logging email', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Určenie typu emailu na základe predmetu a obsahu
     */
    private function determineEmailType(string $subject, ?string $body): string
    {
        $subject = strtolower($subject);
        $body = strtolower($body ?? '');
        
        // Test emaily
        if (strpos($subject, 'test') !== false || strpos($subject, '[test]') !== false) {
            return 'test';
        }
        
        // Uvítacie emaily
        if (strpos($subject, 'vitaj') !== false || strpos($subject, 'uvítac') !== false || strpos($subject, 'welcome') !== false) {
            return 'welcome';
        }
        
        // Support tickets
        if (strpos($subject, 'support') !== false || strpos($subject, 'ticket') !== false || strpos($subject, 'odpoveď na váš') !== false) {
            return 'support';
        }
        
        // Platby
        if (strpos($subject, 'platba') !== false || strpos($subject, 'payment') !== false || strpos($subject, 'faktúra') !== false || strpos($subject, 'invoice') !== false) {
            return 'payment';
        }
        
        // Inzeráty
        if (strpos($subject, 'inzerát') !== false || strpos($subject, 'ad') !== false) {
            if (strpos($subject, 'vytvorený') !== false || strpos($subject, 'created') !== false) {
                return 'ad_created';
            }
            if (strpos($subject, 'aktualizovaný') !== false || strpos($subject, 'updated') !== false) {
                return 'ad_updated';
            }
            return 'ad_created';
        }
        
        // Predplatné
        if (strpos($subject, 'predplatné') !== false || strpos($subject, 'subscription') !== false) {
            if (strpos($subject, 'expir') !== false) {
                return 'subscription_expiring';
            }
            return 'subscription_expired';
        }
        
        // Admin notifikácie
        if (strpos($subject, 'admin') !== false || strpos($subject, 'administr') !== false) {
            return 'admin_notification';
        }
        
        // Kontaktný formulár
        if (strpos($subject, 'kontaktný formulár') !== false || strpos($subject, 'contact form') !== false || strpos($subject, 'nová správa z kontaktného formulára') !== false) {
            return 'contact';
        }
        
        // Notifikácie
        if (strpos($subject, 'notifikácia') !== false || strpos($subject, 'notification') !== false) {
            return 'notification';
        }
        
        // Defaultne všeobecný email
        return 'general';
    }
    
    /**
     * Získanie hlavičiek emailu - kompatibilita s rôznymi API verziami
     */
    private function getHeaders($message): array
    {
        $headers = [];
        
        try {
            if (method_exists($message, 'getHeaders') && $message->getHeaders()) {
                $messageHeaders = $message->getHeaders();
                
                // Symfony 6+ API
                if (method_exists($messageHeaders, 'all')) {
                    foreach ($messageHeaders->all() as $header) {
                        if (method_exists($header, 'getName') && method_exists($header, 'getBodyAsString')) {
                            $headers[$header->getName()] = $header->getBodyAsString();
                        }
                    }
                }
                
                // Fallback pre starší API
                if (empty($headers) && method_exists($messageHeaders, 'toArray')) {
                    $headers = $messageHeaders->toArray();
                }
            }
        } catch (\Exception $e) {
            Log::warning('LogSentEmail: Could not extract headers', [
                'error' => $e->getMessage()
            ]);
        }
        
        return $headers;
    }
}
