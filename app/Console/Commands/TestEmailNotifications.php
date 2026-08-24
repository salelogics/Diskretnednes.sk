<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\SupportTicket;
use App\Mail\PaymentCompletedMail;
use App\Mail\SubscriptionExpiringMail;
use App\Mail\SupportTicketResponseMail;
use App\Mail\AdminNotificationMail;
use App\Mail\WelcomeMail;
use App\Mail\NewAdCreatedMail;
use Illuminate\Support\Facades\Mail;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notifications {type?} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testuje emailové notifikácie';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->option('email') ?: 'admin@diskretnednes.sk';

        if (!$type) {
            $this->info('Dostupné typy testov:');
            $this->line('1. welcome - Test uvítacieho emailu');
            $this->line('2. new-ad - Test emailu pre nový inzerát');
            $this->line('3. payment - Test úspešnej platby');
            $this->line('4. expiring - Test expirujúceho predplatného');
            $this->line('5. support - Test support ticket odpovede');
            $this->line('6. admin - Test admin notifikácie');
            $this->line('7. all - Všetky testy');
            
            $type = $this->choice('Vyberte typ testu:', [
                'welcome', 'new-ad', 'payment', 'expiring', 'support', 'admin', 'all'
            ]);
        }

        $this->info("Testujem emailové notifikácie pre: {$email}");

        switch ($type) {
            case 'welcome':
                $this->testWelcomeEmail($email);
                break;
            case 'new-ad':
                $this->testNewAdEmail($email);
                break;
            case 'payment':
                $this->testPaymentEmail($email);
                break;
            case 'expiring':
                $this->testExpiringEmail($email);
                break;
            case 'support':
                $this->testSupportEmail($email);
                break;
            case 'admin':
                $this->testAdminEmail($email);
                break;
            case 'all':
                $this->testWelcomeEmail($email);
                $this->testNewAdEmail($email);
                $this->testPaymentEmail($email);
                $this->testExpiringEmail($email);
                $this->testSupportEmail($email);
                $this->testAdminEmail($email);
                break;
            default:
                $this->error('Neplatný typ testu!');
                return 1;
        }

        $this->info('Testy dokončené!');
        return 0;
    }

    private function testWelcomeEmail($email)
    {
        $this->info('🔄 Testujem uvítací email...');
        
        try {
            $user = User::first() ?: User::factory()->make([
                'email' => $email, 
                'name' => 'Test User',
                'created_at' => now()
            ]);

            Mail::to($email)->send(new WelcomeMail($user));
            $this->info('✅ Uvítací email odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní uvítacieho emailu: ' . $e->getMessage());
        }
    }

    private function testNewAdEmail($email)
    {
        $this->info('🔄 Testujem email pre nový inzerát...');
        
        try {
            $user = User::first() ?: User::factory()->make(['email' => $email, 'name' => 'Test User']);
            $ad = new Ad([
                'id' => 999,
                'user_id' => $user->id ?? 1,
                'nickname' => 'Test Inzerát',
                'ad_type' => 'zena',
                'city' => 'bratislava',
                'status' => 'draft',
                'created_at' => now()
            ]);
            $ad->user = $user;

            Mail::to($email)->send(new NewAdCreatedMail($ad));
            $this->info('✅ Email pre nový inzerát odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní emailu pre nový inzerát: ' . $e->getMessage());
        }
    }

    private function testPaymentEmail($email)
    {
        $this->info('🔄 Testujem email pre úspešnú platbu...');
        
        try {
            // Vytvoríme fake payment objekt
            $user = User::first() ?: User::factory()->make(['email' => $email, 'name' => 'Test User']);
            $ad = Ad::first() ?: Ad::factory()->make(['id' => 123]);
            
            $payment = new AdPayment([
                'payment_id' => 'TEST_' . time(),
                'user_id' => $user->id,
                'ad_id' => $ad->id ?? 123,
                'amount' => 2990,
                'currency' => 'EUR',
                'duration' => 30,
                'is_featured' => true,
                'is_top_ad' => false,
                'subscription_ends_at' => now()->addDays(30),
                'status' => 'completed'
            ]);
            
            $payment->user = $user;
            $payment->ad = $ad;

            Mail::to($email)->send(new PaymentCompletedMail($payment));
            $this->info('✅ Email pre úspešnú platbu odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní emailu pre platbu: ' . $e->getMessage());
        }
    }

    private function testExpiringEmail($email)
    {
        $this->info('🔄 Testujem email pre expirujúce predplatné...');
        
        try {
            $user = User::first() ?: User::factory()->make(['email' => $email, 'name' => 'Test User']);
            $ad = new Ad([
                'id' => 456,
                'user_id' => $user->id,
                'subscription_expires_at' => now()->addDays(3)
            ]);
            $ad->user = $user;

            Mail::to($email)->send(new SubscriptionExpiringMail($ad, 3));
            $this->info('✅ Email pre expirujúce predplatné odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní emailu pre expirujúce predplatné: ' . $e->getMessage());
        }
    }

    private function testSupportEmail($email)
    {
        $this->info('🔄 Testujem email pre support ticket...');
        
        try {
            $user = User::first() ?: User::factory()->make(['email' => $email, 'name' => 'Test User']);
            $ticket = new SupportTicket([
                'id' => 789,
                'user_id' => $user->id,
                'subject' => 'Test support ticket',
                'message' => 'Toto je testovacia správa',
                'admin_response' => 'Toto je testovacia odpoveď od administrátora',
                'status' => 'resolved',
                'created_at' => now()->subHours(2),
                'updated_at' => now()
            ]);
            $ticket->user = $user;

            Mail::to($email)->send(new SupportTicketResponseMail($ticket));
            $this->info('✅ Email pre support ticket odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní emailu pre support ticket: ' . $e->getMessage());
        }
    }

    private function testAdminEmail($email)
    {
        $this->info('🔄 Testujem admin email...');
        
        try {
            Mail::to($email)->send(new AdminNotificationMail(
                'Test admin notifikácia',
                'Toto je testovacia admin notifikácia pre overenie funkčnosti emailového systému.',
                'system',
                'high',
                route('admin.nastenka'),
                'Otvoriť admin panel'
            ));
            $this->info('✅ Admin email odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní admin emailu: ' . $e->getMessage());
        }
    }
}
