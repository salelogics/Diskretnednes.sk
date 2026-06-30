<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailLog;
use Exception;

class QuickEmailTest extends Command
{
    protected $signature = 'email:quick-test 
                            {email : Email address to test}
                            {--log : Log to file instead of sending}';
    
    protected $description = 'Quick email test with logging verification';

    public function handle()
    {
        $email = $this->argument('email');
        $logOnly = $this->option('log');
        
        $this->info("📧 Quick Email Test");
        $this->line("==================");
        $this->line("Target: {$email}");
        $this->line("Mode: " . ($logOnly ? "LOG ONLY" : "SEND"));
        $this->line("");
        
        // Nastavíme synchronne odosielanie
        config(['queue.default' => 'sync']);
        
        // Ak je log mode, nastavíme mail driver na log
        if ($logOnly) {
            config(['mail.default' => 'log']);
        }
        
        // Vyčistíme mail manager cache
        app('mail.manager')->purge();
        
        // Počet emailov pred testom
        $beforeCount = EmailLog::count();
        $this->line("📊 EmailLog before: {$beforeCount}");
        
        try {
            $testTime = now()->format('d.m.Y H:i:s');
            $subject = "🧪 Quick Email Test - {$testTime}";
            
            $this->line("📧 Sending test email...");
            
            Mail::raw(
                "🧪 Quick Email Test\n\n" .
                "Time: {$testTime}\n" .
                "Server: " . gethostname() . "\n" .
                "URL: " . config('app.url') . "\n" .
                "Mail Driver: " . config('mail.default') . "\n" .
                "SMTP Host: " . config('mail.mailers.smtp.host') . "\n" .
                "SMTP Port: " . config('mail.mailers.smtp.port') . "\n\n" .
                "If you received this email, SMTP is working correctly!",
                function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                }
            );
            
            $this->info("✅ Email sent successfully!");
            
            // Počkáme na spracovanie
            sleep(2);
            
            // Počet emailov po teste
            $afterCount = EmailLog::count();
            $newEmails = $afterCount - $beforeCount;
            
            $this->line("📊 EmailLog after: {$afterCount}");
            $this->line("➕ New emails logged: {$newEmails}");
            
            if ($newEmails > 0) {
                $this->info("✅ Email logging: WORKING");
                
                // Zobrazíme posledný email
                $lastEmail = EmailLog::latest()->first();
                if ($lastEmail) {
                    $this->line("📧 Last logged email:");
                    $this->line("   To: {$lastEmail->to_email}");
                    $this->line("   Subject: {$lastEmail->subject}");
                    $this->line("   Type: {$lastEmail->type}");
                    $this->line("   Status: {$lastEmail->status}");
                    $this->line("   Time: {$lastEmail->created_at->format('d.m.Y H:i:s')}");
                }
            } else {
                $this->error("❌ Email logging: NOT WORKING");
            }
            
            if ($logOnly) {
                $this->line("");
                $this->info("📝 Email was logged to: storage/logs/laravel.log");
                $this->line("Check the log file for the email content.");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Email sending failed: " . $e->getMessage());
            $this->line("Error details: " . $e->getFile() . ":" . $e->getLine());
        }
        
        return 0;
    }
} 