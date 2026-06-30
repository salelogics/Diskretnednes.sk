<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;
use Exception;

class DirectEmailTest extends Command
{
    protected $signature = 'email:direct-test 
                            {email : Email address to test}
                            {--subject= : Custom subject}
                            {--bypass-all : Bypass all Laravel email systems}';
    
    protected $description = 'Direct email test that completely bypasses queue and Mailable classes';

    public function handle()
    {
        $email = $this->argument('email');
        $customSubject = $this->option('subject');
        $bypassAll = $this->option('bypass-all');
        
        $this->info("🚀 Direct Email Test (NO QUEUE)");
        $this->line("==================================");
        $this->line("Target: {$email}");
        $this->line("Bypass all: " . ($bypassAll ? "YES" : "NO"));
        $this->line("");
        
        if ($bypassAll) {
            return $this->phpMailerTest($email, $customSubject);
        }
        
        // Krok 1: Úplne vypnúť queue
        $this->info("📋 1. Vypínam queue systém...");
        $originalQueue = config('queue.default');
        config(['queue.default' => 'sync']);
        $this->line("   Queue driver zmenený z '{$originalQueue}' na 'sync'");
        
        // Krok 2: Vyčistiť mail manager cache
        $this->info("📧 2. Čistím mail manager cache...");
        app('mail.manager')->purge();
        $this->line("   Mail manager cache vyčistený");
        
        // Krok 3: Nastaviť synchronne odosielanie
        $this->info("⚙️ 3. Nastavujem synchronne odosielanie...");
        $this->line("   Queue connection: " . config('queue.default'));
        $this->line("   Mail driver: " . config('mail.default'));
        
        // Krok 4: Počet emailov pred testom
        $beforeCount = EmailLog::count();
        $this->line("   📊 EmailLog before: {$beforeCount}");
        
        // Krok 5: Test odosielania PRIAMO cez Mail::raw (bez Mailable tried)
        $this->info("📤 4. Odosielam email PRIAMO (bez Mailable tried)...");
        
        try {
            $testTime = now()->format('d.m.Y H:i:s');
            $subject = $customSubject ?: "🚀 Direct Email Test - {$testTime}";
            
            $content = "🚀 DIRECT EMAIL TEST - BEZ QUEUE\n\n";
            $content .= "Čas: {$testTime}\n";
            $content .= "Server: " . gethostname() . "\n";
            $content .= "URL: " . config('app.url') . "\n";
            $content .= "Queue driver: " . config('queue.default') . "\n";
            $content .= "Mail driver: " . config('mail.default') . "\n";
            $content .= "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
            $content .= "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
            $content .= "SMTP Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
            $content .= "From: " . config('mail.from.address') . "\n\n";
            $content .= "Tento email bol odoslaný PRIAMO bez queue systému!\n";
            $content .= "Ak ste dostali tento email, SMTP funguje správne.";
            
            // Odošleme email PRIAMO cez Mail::raw
            Mail::raw($content, function ($message) use ($email, $subject) {
                $message->to($email)
                       ->subject($subject)
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("   ✅ Email odoslaný PRIAMO!");
            
            // Počkáme na spracovanie
            sleep(3);
            
            // Krok 6: Skontrolovať výsledky
            $afterCount = EmailLog::count();
            $newEmails = $afterCount - $beforeCount;
            
            $this->line("   📊 EmailLog after: {$afterCount}");
            $this->line("   ➕ New emails logged: {$newEmails}");
            
            if ($newEmails > 0) {
                $this->info("   ✅ Email logging: FUNGUJE");
                
                // Zobrazíme posledný email
                $lastEmail = EmailLog::latest()->first();
                if ($lastEmail) {
                    $this->line("   📧 Posledný email v logu:");
                    $this->line("      To: {$lastEmail->to_email}");
                    $this->line("      Subject: {$lastEmail->subject}");
                    $this->line("      Type: {$lastEmail->type}");
                    $this->line("      Status: {$lastEmail->status}");
                    $this->line("      Time: {$lastEmail->created_at->format('d.m.Y H:i:s')}");
                }
            } else {
                $this->error("   ❌ Email logging: NEFUNGUJE");
            }
            
            $this->line("");
            $this->info("🎯 Výsledky:");
            if ($newEmails > 0) {
                $this->info("✅ SMTP a Email logging funguje!");
                $this->info("❌ Problém je v QUEUE systéme - emaily sa hromadia vo fronte");
                $this->line("");
                $this->warn("🔧 Riešenie:");
                $this->line("1. Spustite queue worker: php artisan queue:work");
                $this->line("2. Alebo odstráňte 'implements ShouldQueue' z Mailable tried");
                $this->line("3. Alebo nastavte QUEUE_CONNECTION=sync v .env");
            } else {
                $this->error("❌ SMTP alebo Email logging nefunguje");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Chyba pri odosielaní emailu: " . $e->getMessage());
            $this->line("   File: " . $e->getFile() . ":" . $e->getLine());
            $this->line("   Trace: " . $e->getTraceAsString());
        }
        
        // Obnovíme pôvodné nastavenia
        config(['queue.default' => $originalQueue]);
        
        return 0;
    }
    
    private function phpMailerTest($email, $customSubject)
    {
        $this->info("🔥 PHP Native Email Test (úplne bez Laravel)");
        $this->line("================================================");
        
        try {
            $testTime = now()->format('d.m.Y H:i:s');
            $subject = $customSubject ?: "🔥 PHP Native Email Test - {$testTime}";
            
            $content = "🔥 PHP NATIVE EMAIL TEST\n\n";
            $content .= "Čas: {$testTime}\n";
            $content .= "Server: " . gethostname() . "\n";
            $content .= "PHP Version: " . PHP_VERSION . "\n";
            $content .= "Tento email bol odoslaný pomocou PHP mail() funkcie!\n";
            $content .= "Úplne bez Laravel, bez queue, bez Mailable tried.\n\n";
            $content .= "Ak ste dostali tento email, PHP mail() funguje.";
            
            $headers = "From: " . config('mail.from.address') . "\r\n";
            $headers .= "Reply-To: " . config('mail.from.address') . "\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
            $headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";
            
            if (mail($email, $subject, $content, $headers)) {
                $this->info("✅ PHP mail() úspešne odoslaný!");
                $this->line("   Recipient: {$email}");
                $this->line("   Subject: {$subject}");
                
                $this->line("");
                $this->info("🎯 Výsledky:");
                $this->info("✅ PHP mail() funkcia funguje!");
                $this->info("❌ Problém je v Laravel Mail systéme alebo SMTP konfigurácii");
                
            } else {
                $this->error("❌ PHP mail() zlyhalo!");
                $this->line("   Možné problémy:");
                $this->line("   - Server nemá nakonfigurovaný mail server");
                $this->line("   - PHP mail() funkcia je zakázaná");
                $this->line("   - Firewall blokuje odchádziu poštu");
            }
            
        } catch (Exception $e) {
            $this->error("❌ PHP mail() test zlyhal: " . $e->getMessage());
        }
        
        return 0;
    }
} 