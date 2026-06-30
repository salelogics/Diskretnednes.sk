<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use App\Models\EmailLog;
use App\Listeners\LogSentEmail;
use Exception;

class DiagnoseEmailSystem extends Command
{
    protected $signature = 'diagnose:email-system 
                            {--test-email= : Email address for testing}
                            {--fix : Try to fix common issues}';
    
    protected $description = 'Comprehensive email system diagnostics for development environment';

    public function handle()
    {
        $this->info('🔍 Email System Diagnostics - Development Environment');
        $this->line('=========================================================');
        
        // Test email
        $testEmail = $this->option('test-email') ?: 'test@gmail.com';
        $fix = $this->option('fix');
        
        $this->line("Test email: {$testEmail}");
        $this->line("Fix mode: " . ($fix ? 'ON' : 'OFF'));
        $this->line('');
        
        // Krok 1: Kontrola databázy
        $this->checkDatabase();
        
        // Krok 2: Kontrola event listenerov
        $this->checkEventListeners();
        
        // Krok 3: Kontrola SMTP konfigurácie
        $this->checkSMTPConfiguration();
        
        // Krok 4: Test odosielania
        $this->testEmailSending($testEmail);
        
        // Krok 5: Kontrola queue
        $this->checkQueue();
        
        // Krok 6: Kontrola email logov
        $this->checkEmailLogs();
        
        // Krok 7: Opravy ak je --fix
        if ($fix) {
            $this->applyFixes();
        }
        
        $this->line('');
        $this->info('🎉 Diagnostika dokončená!');
        
        return 0;
    }
    
    private function checkDatabase()
    {
        $this->info('📊 1. Kontrola databázy...');
        
        try {
            // Test databázového pripojenia
            DB::connection()->getPdo();
            $this->line('   ✅ Databázové pripojenie: OK');
        } catch (Exception $e) {
            $this->error('   ❌ Databázové pripojenie: CHYBA - ' . $e->getMessage());
            return;
        }
        
        // Kontrola email_logs tabuľky
        try {
            $tableExists = DB::getSchemaBuilder()->hasTable('email_logs');
            if ($tableExists) {
                $this->line('   ✅ Tabuľka email_logs: existuje');
                $count = EmailLog::count();
                $this->line("   📊 Počet emailov v logu: {$count}");
            } else {
                $this->error('   ❌ Tabuľka email_logs: neexistuje');
                $this->warn('   💡 Spustite: php artisan migrate');
            }
        } catch (Exception $e) {
            $this->error('   ❌ Chyba pri kontrole tabuľky: ' . $e->getMessage());
        }
        
        $this->line('');
    }
    
    private function checkEventListeners()
    {
        $this->info('🎧 2. Kontrola event listenerov...');
        
        // Kontrola registrácie event listenera
        $listeners = Event::getListeners(MessageSent::class);
        
        if (empty($listeners)) {
            $this->error('   ❌ MessageSent event listener: nie je registrovaný');
            $this->warn('   💡 Skontrolujte EmailLogServiceProvider');
        } else {
            $this->line('   ✅ MessageSent event listener: registrovaný');
            
            foreach ($listeners as $listener) {
                if (is_string($listener)) {
                    $this->line("   📝 Listener: {$listener}");
                } else {
                    $this->line("   📝 Listener: " . get_class($listener));
                }
            }
        }
        
        // Test manuálneho spustenia listenera
        try {
            $logSentEmail = new LogSentEmail();
            $this->line('   ✅ LogSentEmail trieda: inicializovateľná');
        } catch (Exception $e) {
            $this->error('   ❌ LogSentEmail trieda: chyba - ' . $e->getMessage());
        }
        
        $this->line('');
    }
    
    private function checkSMTPConfiguration()
    {
        $this->info('📧 3. Kontrola SMTP konfigurácie...');
        
        $config = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '***set***' : 'NOT SET',
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];
        
        foreach ($config as $key => $value) {
            if (empty($value) && $key !== 'MAIL_PASSWORD') {
                $this->error("   ❌ {$key}: nie je nastavené");
            } elseif ($key === 'MAIL_PASSWORD' && $value === 'NOT SET') {
                $this->error("   ❌ {$key}: nie je nastavené");
            } else {
                $this->line("   ✅ {$key}: {$value}");
            }
        }
        
        // Test SMTP pripojenia
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        
        if ($host && $port) {
            $this->line('   🔌 Test SMTP pripojenia...');
            $socket = @fsockopen($host, $port, $errno, $errstr, 10);
            if ($socket) {
                $this->line("   ✅ SMTP pripojenie na {$host}:{$port}: OK");
                fclose($socket);
            } else {
                $this->error("   ❌ SMTP pripojenie na {$host}:{$port}: CHYBA - {$errstr}");
            }
        }
        
        $this->line('');
    }
    
    private function testEmailSending($testEmail)
    {
        $this->info('📤 4. Test odosielania emailu...');
        
        $beforeCount = EmailLog::count();
        $this->line("   📊 EmailLog počet pred testom: {$beforeCount}");
        
        try {
            // Nastavíme synchronne odosielanie
            config(['queue.default' => 'sync']);
            
            // Vyčistíme mail manager cache
            app('mail.manager')->purge();
            
            $this->line('   📧 Odosielam test email...');
            
            Mail::raw(
                "🧪 Test email z diagnostiky\n\nČas: " . now()->format('d.m.Y H:i:s') . "\nServer: " . gethostname() . "\nURL: " . config('app.url'),
                function ($message) use ($testEmail) {
                    $message->to($testEmail)
                           ->subject('🧪 Diagnostika Email System - ' . now()->format('d.m.Y H:i:s'));
                }
            );
            
            $this->line('   ✅ Email odoslanie: úspešné');
            
            // Počkáme na spracovanie
            sleep(2);
            
            $afterCount = EmailLog::count();
            $newEmails = $afterCount - $beforeCount;
            
            $this->line("   📊 EmailLog počet po teste: {$afterCount}");
            $this->line("   ➕ Nové emaily v logu: {$newEmails}");
            
            if ($newEmails > 0) {
                $this->line('   ✅ Email logging: funguje');
            } else {
                $this->error('   ❌ Email logging: nefunguje');
            }
            
        } catch (Exception $e) {
            $this->error('   ❌ Email odoslanie: CHYBA - ' . $e->getMessage());
            $this->line('   📝 Error details: ' . $e->getFile() . ':' . $e->getLine());
        }
        
        $this->line('');
    }
    
    private function checkQueue()
    {
        $this->info('⏳ 5. Kontrola queue systému...');
        
        $queueConnection = config('queue.default');
        $this->line("   📊 Queue driver: {$queueConnection}");
        
        try {
            // Test queue pripojenia
            if ($queueConnection === 'database') {
                $jobsCount = DB::table('jobs')->count();
                $this->line("   📊 Jobs vo fronte: {$jobsCount}");
                
                $failedJobsCount = DB::table('failed_jobs')->count();
                $this->line("   📊 Failed jobs: {$failedJobsCount}");
            }
            
            $this->line('   ✅ Queue systém: dostupný');
            
        } catch (Exception $e) {
            $this->error('   ❌ Queue systém: chyba - ' . $e->getMessage());
        }
        
        $this->line('');
    }
    
    private function checkEmailLogs()
    {
        $this->info('📋 6. Kontrola email logov...');
        
        try {
            $recentEmails = EmailLog::latest()->limit(5)->get();
            
            if ($recentEmails->isEmpty()) {
                $this->warn('   ⚠️  Žiadne emaily v logu');
            } else {
                $this->line('   📊 Posledné emaily:');
                foreach ($recentEmails as $email) {
                    $date = $email->created_at->format('d.m.Y H:i');
                    $subject = substr($email->subject, 0, 40) . '...';
                    $this->line("   📧 {$date} - {$email->to_email} - {$subject}");
                }
            }
            
        } catch (Exception $e) {
            $this->error('   ❌ Chyba pri čítaní email logov: ' . $e->getMessage());
        }
        
        $this->line('');
    }
    
    private function applyFixes()
    {
        $this->info('🔧 7. Aplikovanie opráv...');
        
        // Fix 1: Vyčistenie cache
        $this->line('   🧹 Čistenie cache...');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('route:clear');
        
        // Fix 2: Kontrola migrácie
        $this->line('   🗄️  Kontrola migrácie...');
        if (!DB::getSchemaBuilder()->hasTable('email_logs')) {
            $this->line('   📝 Spúšťam migráciu email_logs...');
            $this->call('migrate', ['--force' => true]);
        }
        
        // Fix 3: Nastavenie správneho queue drivera
        $this->line('   ⚙️  Nastavenie queue na sync...');
        config(['queue.default' => 'sync']);
        
        // Fix 4: Vyčistenie mail manager cache
        $this->line('   📧 Vyčistenie mail manager cache...');
        app('mail.manager')->purge();
        
        $this->line('   ✅ Opravy aplikované');
        $this->line('');
    }
} 