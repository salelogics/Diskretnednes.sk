<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Ad;
use App\Models\EmailLog;
use App\Models\AdPayment;
use App\Models\SupportTicket;
use App\Mail\WelcomeMail;
use App\Mail\NewAdCreatedMail;
use App\Mail\PaymentCompletedMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class TestSpecificEmails extends Command
{
    protected $signature = 'test:specific-emails 
                            {email : Email address to test}
                            {--type= : Specific type to test (registration, ads, payment, support)}';
    
    protected $description = 'Test specific email types that users report as not working';

    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        
        $this->info("🔧 Test konkrétnych emailov");
        $this->line("==========================");
        $this->line("Target: {$email}");
        $this->line("Type: " . ($type ?: 'all'));
        $this->line("");
        
        // Zobrazenie aktuálnej konfigurácie
        $this->displayConfig();
        
        // Počet emailov pred testom
        $beforeCount = EmailLog::count();
        $this->line("📊 EmailLog before: {$beforeCount}");
        $this->line("");
        
        // Testy podľa typu
        switch ($type) {
            case 'registration':
                $this->testRegistrationEmails($email);
                break;
            case 'ads':
                $this->testAdEmails($email);
                break;
            case 'payment':
                $this->testPaymentEmails($email);
                break;
            case 'support':
                $this->testSupportEmails($email);
                break;
            default:
                $this->testAllEmails($email);
        }
        
        // Počkáme na spracovanie
        sleep(5);
        
        // Výsledky
        $afterCount = EmailLog::count();
        $newEmails = $afterCount - $beforeCount;
        
        $this->line("");
        $this->info("📊 Výsledky:");
        $this->line("EmailLog after: {$afterCount}");
        $this->line("New emails: {$newEmails}");
        
        if ($newEmails > 0) {
            $this->info("✅ Emaily fungujú!");
            $this->displayRecentEmails();
        } else {
            $this->error("❌ Žiadne emaily neboli odoslané - problém stále existuje");
        }
        
        return 0;
    }
    
    private function displayConfig()
    {
        $this->info("⚙️ Konfigurácia:");
        $this->line("Mail driver: " . config('mail.default'));
        $this->line("SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->line("SMTP Port: " . config('mail.mailers.smtp.port'));
        $this->line("From: " . config('mail.from.address'));
        $this->line("Queue driver: " . config('queue.default'));
        $this->line("");
    }
    
    private function testRegistrationEmails($email)
    {
        $this->info("👤 Test registračných emailov...");
        
        try {
            // Vytvoríme mock user
            $mockUser = new User([
                'id' => 999,
                'name' => 'Test User',
                'email' => $email,
                'created_at' => now()
            ]);
            
            // Test 1: Priamy WelcomeMail
            $this->line("   1. Test WelcomeMail (bez queue)...");
            Mail::to($email)->send(new WelcomeMail($mockUser));
            $this->info("      ✅ WelcomeMail odoslaný");
            
            // Test 2: Cez NotificationService
            $this->line("   2. Test NotificationService->userRegistered...");
            $this->notificationService->userRegistered($mockUser);
            $this->info("      ✅ NotificationService userRegistered volaný");
            
            $this->info("✅ Registračné emaily: ÚSPEŠNÉ");
            
        } catch (Exception $e) {
            $this->error("❌ Registračné emaily: CHYBA - " . $e->getMessage());
            $this->line("   Error: " . $e->getFile() . ":" . $e->getLine());
        }
    }
    
    private function testAdEmails($email)
    {
        $this->info("📄 Test emailov o inzerátoch...");
        
        try {
            // Vytvoríme mock ad
            $mockAd = new Ad([
                'id' => 999,
                'user_id' => 1,
                'nickname' => 'Test Inzerát',
                'ad_type' => 'zena',
                'city' => 'bratislava',
                'status' => 'active',
                'created_at' => now()
            ]);
            
            // Mock user pre ad
            $mockUser = new User([
                'id' => 1,
                'name' => 'Test User',
                'email' => $email,
            ]);
            
            // Nastavíme reláciu
            $mockAd->setRelation('user', $mockUser);
            
            // Test 1: Priamy NewAdCreatedMail
            $this->line("   1. Test NewAdCreatedMail (bez queue)...");
            Mail::to($email)->send(new NewAdCreatedMail($mockAd));
            $this->info("      ✅ NewAdCreatedMail odoslaný");
            
            // Test 2: Cez NotificationService
            $this->line("   2. Test NotificationService->adCreated...");
            $this->notificationService->adCreated($mockAd);
            $this->info("      ✅ NotificationService adCreated volaný");
            
            $this->info("✅ Inzerátové emaily: ÚSPEŠNÉ");
            
        } catch (Exception $e) {
            $this->error("❌ Inzerátové emaily: CHYBA - " . $e->getMessage());
            $this->line("   Error: " . $e->getFile() . ":" . $e->getLine());
        }
    }
    
    private function testPaymentEmails($email)
    {
        $this->info("💳 Test platobných emailov...");
        
        try {
            // Vytvoríme mock payment
            $mockPayment = new AdPayment([
                'id' => 999,
                'user_id' => 1,
                'ad_id' => 999,
                'amount' => 25.00,
                'currency' => 'EUR',
                'status' => 'completed',
                'payment_method' => 'stripe',
                'created_at' => now()
            ]);
            
            // Test 1: Priamy PaymentCompletedMail
            $this->line("   1. Test PaymentCompletedMail (bez queue)...");
            Mail::to($email)->send(new PaymentCompletedMail($mockPayment));
            $this->info("      ✅ PaymentCompletedMail odoslaný");
            
            // Test 2: Cez NotificationService
            $this->line("   2. Test NotificationService->paymentCompleted...");
            $this->notificationService->paymentCompleted($mockPayment);
            $this->info("      ✅ NotificationService paymentCompleted volaný");
            
            $this->info("✅ Platobné emaily: ÚSPEŠNÉ");
            
        } catch (Exception $e) {
            $this->error("❌ Platobné emaily: CHYBA - " . $e->getMessage());
            $this->line("   Error: " . $e->getFile() . ":" . $e->getLine());
        }
    }
    
    private function testSupportEmails($email)
    {
        $this->info("🎧 Test support emailov...");
        
        try {
            // Vytvoríme mock support ticket
            $mockTicket = new SupportTicket([
                'id' => 999,
                'user_id' => 1,
                'name' => 'Test User',
                'email' => $email,
                'subject' => 'Test Support Ticket',
                'message' => 'Test message',
                'status' => 'new',
                'created_at' => now()
            ]);
            
            // Test NotificationService
            $this->line("   1. Test NotificationService->newSupportTicket...");
            $this->notificationService->newSupportTicket($mockTicket);
            $this->info("      ✅ NotificationService newSupportTicket volaný");
            
            $this->info("✅ Support emaily: ÚSPEŠNÉ");
            
        } catch (Exception $e) {
            $this->error("❌ Support emaily: CHYBA - " . $e->getMessage());
            $this->line("   Error: " . $e->getFile() . ":" . $e->getLine());
        }
    }
    
    private function testAllEmails($email)
    {
        $this->info("🔄 Test všetkých typov emailov...");
        $this->line("");
        
        $this->testRegistrationEmails($email);
        $this->line("");
        
        $this->testAdEmails($email);
        $this->line("");
        
        $this->testPaymentEmails($email);
        $this->line("");
        
        $this->testSupportEmails($email);
    }
    
    private function displayRecentEmails()
    {
        $this->line("");
        $this->info("📧 Posledných 5 emailov v logu:");
        
        $recentEmails = EmailLog::latest()->limit(5)->get();
        
        foreach ($recentEmails as $email) {
            $this->line("   " . $email->created_at->format('H:i:s') . " - " . $email->to_email . " - " . $email->subject);
        }
    }
} 