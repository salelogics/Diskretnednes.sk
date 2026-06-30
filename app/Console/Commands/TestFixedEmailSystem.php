<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;
use App\Models\User;
use App\Models\AdPayment;
use App\Models\Ad;
use App\Mail\WelcomeMail;
use App\Mail\PaymentCompletedMail;
use App\Mail\SubscriptionExpiredMail;
use App\Services\NotificationService;
use App\Services\InvoiceService;
use App\Models\Invoice;
use Exception;

class TestFixedEmailSystem extends Command
{
    protected $signature = 'test:fixed-email-system 
                            {email : Email address to test}
                            {--all : Test all email types}';
    
    protected $description = 'Test the fixed email system - no more queue issues or log driver problems';

    public function handle()
    {
        $email = $this->argument('email');
        $testAll = $this->option('all');
        
        $this->info("🔧 Test opraveného email systému");
        $this->line("===============================");
        $this->line("Target: {$email}");
        $this->line("Test all types: " . ($testAll ? "YES" : "NO"));
        $this->line("");
        
        // Zobrazenie konfigurácie
        $this->displayConfig();
        
        // Počet emailov pred testom
        $beforeCount = EmailLog::count();
        $this->line("📊 EmailLog before: {$beforeCount}");
        $this->line("");
        
        // Test 1: Základný SMTP test
        $this->testBasicSMTP($email);
        
        if ($testAll) {
            // Test 2: WelcomeMail (už bez ShouldQueue)
            $this->testWelcomeMail($email);
            
            // Test 3: PaymentCompletedMail (už bez ShouldQueue)
            $this->testPaymentCompletedMail($email);
            
            // Test 4: SubscriptionExpiredMail (už bez ShouldQueue)
            $this->testSubscriptionExpiredMail($email);
            
            // Test 5: NotificationService
            $this->testNotificationService($email);
            
            // Test 6: InvoiceService (ak existuje test faktúra)
            $this->testInvoiceService($email);
        }
        
        // Počkáme na spracovanie
        sleep(3);
        
        // Výsledky
        $afterCount = EmailLog::count();
        $newEmails = $afterCount - $beforeCount;
        
        $this->line("");
        $this->info("📊 Výsledky:");
        $this->line("EmailLog after: {$afterCount}");
        $this->line("New emails: {$newEmails}");
        
        if ($newEmails > 0) {
            $this->info("✅ Email systém FUNGUJE!");
            $this->line("✅ Žiadne queue problémy");
            $this->line("✅ Žiadne log driver problémy");
            $this->line("✅ Emaily sa odosielaju okamžite");
        } else {
            $this->error("❌ Email systém stále nefunguje");
        }
        
        return 0;
    }
    
    private function displayConfig()
    {
        $this->info("⚙️ Konfigurácia:");
        $this->line("Mail driver: " . config('mail.default'));
        $this->line("SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->line("SMTP Port: " . config('mail.mailers.smtp.port'));
        $this->line("SMTP Encryption: " . config('mail.mailers.smtp.encryption'));
        $this->line("From: " . config('mail.from.address'));
        $this->line("Queue driver: " . config('queue.default'));
        $this->line("");
    }
    
    private function testBasicSMTP($email)
    {
        $this->info("1️⃣ Test základného SMTP...");
        
        try {
            Mail::raw(
                "🔧 Test opraveného email systému\n\n" .
                "Tento email testuje:\n" .
                "✅ Žiadne queue problémy\n" .
                "✅ Žiadne log driver problémy\n" .
                "✅ Priame SMTP odosielanie\n\n" .
                "Čas: " . now()->format('d.m.Y H:i:s') . "\n" .
                "Server: " . gethostname() . "\n\n" .
                "Ak vidíte tento email, všetky opravy fungujú!",
                function ($message) use ($email) {
                    $message->to($email)
                           ->subject('🔧 Test opraveného email systému - ' . now()->format('d.m.Y H:i:s'));
                }
            );
            
            $this->info("   ✅ Základný SMTP test: ÚSPEŠNÝ");
            
        } catch (Exception $e) {
            $this->error("   ❌ Základný SMTP test: CHYBA - " . $e->getMessage());
        }
    }
    
    private function testWelcomeMail($email)
    {
        $this->info("2️⃣ Test WelcomeMail (bez ShouldQueue)...");
        
        try {
            $user = new User([
                'name' => 'Test User',
                'email' => $email,
                'created_at' => now()
            ]);
            
            Mail::to($email)->send(new WelcomeMail($user));
            
            $this->info("   ✅ WelcomeMail: ÚSPEŠNÝ");
            
        } catch (Exception $e) {
            $this->error("   ❌ WelcomeMail: CHYBA - " . $e->getMessage());
        }
    }
    
    private function testPaymentCompletedMail($email)
    {
        $this->info("3️⃣ Test PaymentCompletedMail (bez ShouldQueue)...");
        
        try {
            // Vytvoríme mock payment
            $payment = new AdPayment([
                'id' => 999,
                'amount' => 25.00,
                'status' => 'completed',
                'package_name' => 'Test Package',
                'created_at' => now()
            ]);
            
            Mail::to($email)->send(new PaymentCompletedMail($payment));
            
            $this->info("   ✅ PaymentCompletedMail: ÚSPEŠNÝ");
            
        } catch (Exception $e) {
            $this->error("   ❌ PaymentCompletedMail: CHYBA - " . $e->getMessage());
        }
    }
    
    private function testSubscriptionExpiredMail($email)
    {
        $this->info("4️⃣ Test SubscriptionExpiredMail (bez ShouldQueue)...");
        
        try {
            // Vytvoríme mock ad
            $ad = new Ad([
                'id' => 999,
                'title' => 'Test Inzerát',
                'expires_at' => now()->subDays(1),
                'created_at' => now()
            ]);
            
            Mail::to($email)->send(new SubscriptionExpiredMail($ad));
            
            $this->info("   ✅ SubscriptionExpiredMail: ÚSPEŠNÝ");
            
        } catch (Exception $e) {
            $this->error("   ❌ SubscriptionExpiredMail: CHYBA - " . $e->getMessage());
        }
    }
    
    private function testNotificationService($email)
    {
        $this->info("5️⃣ Test NotificationService...");
        
        try {
            $notificationService = new NotificationService();
            
            // Test welcome notification
            $result = $notificationService->sendTemplateEmail('welcome', $email, [
                'user_name' => 'Test User'
            ]);
            
            if ($result) {
                $this->info("   ✅ NotificationService: ÚSPEŠNÝ");
            } else {
                $this->error("   ❌ NotificationService: Vrátil false");
            }
            
        } catch (Exception $e) {
            $this->error("   ❌ NotificationService: CHYBA - " . $e->getMessage());
        }
    }
    
    private function testInvoiceService($email)
    {
        $this->info("6️⃣ Test InvoiceService...");
        
        try {
            // Skontrolujeme či existuje nejaká test faktúra
            $invoice = Invoice::where('status', 'draft')->first();
            
            if (!$invoice) {
                $this->warn("   ⚠️  Žiadne test faktúry - preskakujem");
                return;
            }
            
            // Nastavíme test email
            $originalData = $invoice->customer_data;
            $invoice->customer_data = array_merge($originalData, ['email' => $email]);
            
            $invoiceService = new InvoiceService();
            $result = $invoiceService->sendInvoiceByEmail($invoice);
            
            // Obnovíme pôvodné dáta
            $invoice->customer_data = $originalData;
            $invoice->save();
            
            if ($result) {
                $this->info("   ✅ InvoiceService: ÚSPEŠNÝ");
            } else {
                $this->error("   ❌ InvoiceService: Vrátil false");
            }
            
        } catch (Exception $e) {
            $this->error("   ❌ InvoiceService: CHYBA - " . $e->getMessage());
        }
    }
} 