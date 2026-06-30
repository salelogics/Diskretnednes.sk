<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\Invoice;

class ShowTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zobrazí prehľad testovacích dát v databáze';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Prehľad testovacích dát v databáze:');
        $this->line('');

        // Testovacie používatelia
        $testUsers = User::whereIn('email', [
            'test@example.com',
            'user1@example.com',
            'user2@example.com',
            'user3@example.com',
            'user4@example.com',
            'user5@example.com'
        ])->orWhere('name', 'like', '%Test%')->get();

        if ($testUsers->count() > 0) {
            $this->warn("👤 Testovacie používatelia ({$testUsers->count()}):");
            $headers = ['ID', 'Meno', 'Email', 'Vytvorený'];
            $rows = [];
            foreach ($testUsers as $user) {
                $rows[] = [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->created_at->format('d.m.Y H:i')
                ];
            }
            $this->table($headers, $rows);
            $this->line('');
        } else {
            $this->info('✅ Žiadni testovacie používatelia nenájdení.');
            $this->line('');
        }

        // Testovacie inzeráty
        $testUserIds = $testUsers->pluck('id')->toArray();
        
        // Inzeráty testovacích používateľov
        $testAds = collect();
        if (!empty($testUserIds)) {
            $testAds = Ad::whereIn('user_id', $testUserIds)->get();
        }

        // Inzeráty s testovacími názvami
        $testNicknameAds = Ad::where('nickname', 'LIKE', '%Test%')
            ->orWhere('nickname', 'LIKE', '%Testovací%')
            ->orWhere('nickname', 'LIKE', '%Inzerát%od%')
            ->orWhere('nickname', 'LIKE', '%SMS Test%')
            ->get();

        $allTestAds = $testAds->merge($testNicknameAds)->unique('id');

        if ($allTestAds->count() > 0) {
            $this->warn("📝 Testovacie inzeráty ({$allTestAds->count()}):");
            $headers = ['ID', 'Nickname', 'Používateľ', 'Mesto', 'Stav', 'Vytvorený'];
            $rows = [];
            foreach ($allTestAds as $ad) {
                $rows[] = [
                    $ad->id,
                    substr($ad->nickname, 0, 30) . (strlen($ad->nickname) > 30 ? '...' : ''),
                    $ad->user ? $ad->user->name : 'N/A',
                    $ad->city,
                    $ad->status,
                    $ad->created_at->format('d.m.Y H:i')
                ];
            }
            $this->table($headers, $rows);
            $this->line('');
        } else {
            $this->info('✅ Žiadne testovacie inzeráty nenájdené.');
            $this->line('');
        }

        // Testovacie platby
        $testAdIds = $allTestAds->pluck('id')->toArray();
        $testPayments = collect();
        if (!empty($testAdIds)) {
            $testPayments = AdPayment::whereIn('ad_id', $testAdIds)->get();
        }

        if ($testPayments->count() > 0) {
            $this->warn("💳 Testovacie platby ({$testPayments->count()}):");
            $headers = ['ID', 'Payment ID', 'Suma', 'Metóda', 'Stav', 'Vytvorené'];
            $rows = [];
            foreach ($testPayments as $payment) {
                $rows[] = [
                    $payment->id,
                    substr($payment->payment_id, 0, 15) . '...',
                    $payment->amount . ' ' . $payment->currency,
                    $payment->payment_method,
                    $payment->status,
                    $payment->created_at->format('d.m.Y H:i')
                ];
            }
            $this->table($headers, $rows);
            $this->line('');
        } else {
            $this->info('✅ Žiadne testovacie platby nenájdené.');
            $this->line('');
        }

        // Testovacie faktúry
        $testPaymentIds = $testPayments->pluck('id')->toArray();
        $testInvoices = collect();
        if (!empty($testPaymentIds)) {
            $testInvoices = Invoice::whereIn('ad_payment_id', $testPaymentIds)->get();
        }

        if ($testInvoices->count() > 0) {
            $this->warn("🧾 Testovacie faktúry ({$testInvoices->count()}):");
            $headers = ['ID', 'Číslo faktúry', 'Suma', 'Stav', 'Vytvorené'];
            $rows = [];
            foreach ($testInvoices as $invoice) {
                $rows[] = [
                    $invoice->id,
                    $invoice->invoice_number,
                    $invoice->total_amount . ' ' . $invoice->currency,
                    $invoice->status,
                    $invoice->created_at->format('d.m.Y H:i')
                ];
            }
            $this->table($headers, $rows);
            $this->line('');
        } else {
            $this->info('✅ Žiadne testovacie faktúry nenájdené.');
            $this->line('');
        }

        // Súhrn
        $this->info('📊 Súhrn:');
        $this->line("   Testovacie používatelia: {$testUsers->count()}");
        $this->line("   Testovacie inzeráty: {$allTestAds->count()}");
        $this->line("   Testovacie platby: {$testPayments->count()}");
        $this->line("   Testovacie faktúry: {$testInvoices->count()}");
        $this->line('');

        if ($testUsers->count() > 0 || $allTestAds->count() > 0) {
            $this->warn('💡 Na vymazanie testovacích dát spustite:');
            $this->line('   php artisan clean:test-data');
        } else {
            $this->info('🎉 Databáza je čistá - žiadne testovacie dáta!');
        }

        return 0;
    }
}
