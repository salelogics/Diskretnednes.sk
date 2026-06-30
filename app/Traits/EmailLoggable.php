<?php

namespace App\Traits;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;

trait EmailLoggable
{
    protected function logEmailAfterSend($to, $subject, $body = null, $type = 'general', $metadata = [])
    {
        try {
            // Ak je to pole, vezmeme prvý email
            if (is_array($to) && count($to) > 0) {
                $to = $to[0]['address'] ?? $to[0];
            }
            
            EmailLog::logEmail($to, $subject, $body, $type, $metadata);
        } catch (\Exception $e) {
            \Log::error('Failed to log email: ' . $e->getMessage());
        }
    }
    
    protected function determineEmailType($subject)
    {
        $subject = strtolower($subject);
        
        if (strpos($subject, 'test') !== false) return 'test';
        if (strpos($subject, 'support') !== false || strpos($subject, 'ticket') !== false) return 'support';
        if (strpos($subject, 'vitaj') !== false || strpos($subject, 'welcome') !== false) return 'welcome';
        if (strpos($subject, 'platba') !== false || strpos($subject, 'payment') !== false) return 'payment';
        if (strpos($subject, 'inzerát') !== false) return 'ad_created';
        if (strpos($subject, 'admin') !== false) return 'admin_notification';
        
        return 'general';
    }
} 