<?php

namespace App\Helpers;

class AdminNotificationHelper
{
    /**
     * Získa zoznam admin email adries z nastavení databázy
     */
    public static function getAdminEmails(): array
    {
        // Najprv skúsime databázu (nový systém)
        $emailsString = \App\Models\Setting::get('admin_notification_emails');
        
        // Fallback na config a env ak nie je v databáze
        if (empty($emailsString)) {
            $emailsString = config('app.admin_notification_emails', '');
        }
        
        if (empty($emailsString)) {
            $emailsString = env('ADMIN_NOTIFICATION_EMAILS', '');
        }
        
        \Log::info('AdminNotificationHelper: Getting admin emails', [
            'emails_string' => $emailsString,
            'db_value' => \App\Models\Setting::get('admin_notification_emails'),
            'config_value' => config('app.admin_notification_emails'),
            'env_value' => env('ADMIN_NOTIFICATION_EMAILS')
        ]);
        
        if (empty($emailsString)) {
            // Fallback na mail_from_address z databázy alebo env
            $fallbackEmail = \App\Models\Setting::get('mail_from_address') ?: env('MAIL_FROM_ADDRESS', 'admin@diskretnednes.sk');
            \Log::info('AdminNotificationHelper: Using fallback email', ['fallback' => $fallbackEmail]);
            return [$fallbackEmail];
        }
        
        // Rozdelíme emaily podľa čiarky a vyčistíme medzery
        $emails = array_map('trim', explode(',', $emailsString));
        
        // Filtrujeme prázdne hodnoty a validujeme emaily
        $validEmails = array_filter($emails, function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        
        \Log::info('AdminNotificationHelper: Processed admin emails', [
            'raw_emails' => $emails,
            'valid_emails' => $validEmails
        ]);
        
        return $validEmails;
    }
    
    /**
     * Pošle email na všetky admin adresy
     */
    public static function sendToAdmins($mailable)
    {
        $adminEmails = self::getAdminEmails();
        
        \Log::info('AdminNotificationHelper: Sending to admins', [
            'admin_emails' => $adminEmails,
            'mailable_class' => get_class($mailable)
        ]);
        
        foreach ($adminEmails as $email) {
            try {
                \Log::info('AdminNotificationHelper: Sending to admin', ['email' => $email]);
                \Log::info('AdminNotificationHelper: About to call Mail::to()->send()', ['email' => $email, 'mailable' => get_class($mailable)]);
                
                \Mail::to($email)->send($mailable);
                
                \Log::info('AdminNotificationHelper: Successfully sent to admin', ['email' => $email]);
                \Log::info('AdminNotificationHelper: Mail::to()->send() completed', ['email' => $email]);
            } catch (\Exception $e) {
                \Log::error("AdminNotificationHelper: Failed to send admin notification to {$email}: " . $e->getMessage(), [
                    'email' => $email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }
    
    /**
     * Odošle jednoduchú text notifikáciu na všetky admin adresy
     */
    public static function sendSimpleNotification(string $subject, string $message): bool
    {
        \Log::info('AdminNotificationHelper: Sending simple notification', [
            'subject' => $subject,
            'message' => substr($message, 0, 100) . '...'
        ]);

        $adminEmails = self::getAdminEmails();
        
        if (empty($adminEmails)) {
            \Log::warning('AdminNotificationHelper: No admin emails found for simple notification');
            return false;
        }

        $success = true;
        foreach ($adminEmails as $email) {
            try {
                \Mail::raw($message, function ($mail) use ($email, $subject) {
                    $mail->to($email)->subject($subject);
                });
                \Log::info('AdminNotificationHelper: Simple notification sent', ['email' => $email]);
            } catch (\Exception $e) {
                \Log::error('AdminNotificationHelper: Failed to send simple notification', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Pošle HTML email na konkrétnu adresu
     */
    public static function sendHtmlToEmail(string $content, string $subject, string $email): bool
    {
        \Log::info('AdminNotificationHelper: Sending HTML email to specific address', [
            'email' => $email,
            'subject' => $subject,
            'content_length' => strlen($content)
        ]);

        try {
            \Mail::html($content, function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });
            
            \Log::info('AdminNotificationHelper: HTML email sent successfully', [
                'email' => $email,
                'subject' => $subject
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('AdminNotificationHelper: Failed to send HTML email', [
                'email' => $email,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * NOVÉ: Odošle Mailable objekt na konkrétnu adresu s použitím admin SMTP nastavení
     */
    public static function sendMailableToEmail($mailable, string $email): bool
    {
        \Log::info('AdminNotificationHelper: Sending mailable to specific address', [
            'email' => $email,
            'mailable_class' => get_class($mailable)
        ]);

        try {
            \Mail::to($email)->send($mailable);
            
            \Log::info('AdminNotificationHelper: Mailable sent successfully', [
                'email' => $email,
                'mailable_class' => get_class($mailable)
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('AdminNotificationHelper: Failed to send mailable', [
                'email' => $email,
                'mailable_class' => get_class($mailable),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
} 