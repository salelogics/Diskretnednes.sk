<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StripeService;
use App\Models\AdPayment;
use App\Models\User;
use App\Models\Ad;
use App\Models\PaymentPackage;
use Illuminate\Support\Facades\DB;

class TestStripeConfig extends Command
{
    protected $signature = 'stripe:test-config';
    protected $description = 'Test Stripe configuration and connectivity';

    public function handle()
    {
        $this->info('=== STRIPE KONFIGURÁCIA TEST ===');
        $this->newLine();

        // Test 1: Konfigurácia
        $this->info('1. Kontrola konfigurácie...');
        
        // Načítame z admin nastavení (s fallback na .env)
        $stripeKey = \App\Models\Setting::get('stripe_publishable_key') ?? config('services.stripe.key');
        $stripeSecret = \App\Models\Setting::get('stripe_secret_key') ?? config('services.stripe.secret');
        
        $this->line('   Stripe Publishable Key (Admin/ENV): ' . ($stripeKey ? 'NASTAVENÝ (' . substr($stripeKey, 0, 12) . '...)' : 'CHÝBA'));
        $this->line('   Stripe Secret Key (Admin/ENV): ' . ($stripeSecret ? 'NASTAVENÝ (' . substr($stripeSecret, 0, 12) . '...)' : 'CHÝBA'));
        
        // Info o source
        $adminKey = \App\Models\Setting::get('stripe_publishable_key');
        $adminSecret = \App\Models\Setting::get('stripe_secret_key');
        $this->line('   Zdroj kľúčov: ' . ($adminKey && $adminSecret ? 'ADMIN PANEL' : 'ENV SÚBOR'));
        
        if (!$stripeKey || !$stripeSecret) {
            $this->error('   Stripe kľúče nie sú nakonfigurované!');
            return;
        }
        $this->newLine();

        // Test 2: Database test
        $this->info('2. Kontrola databázy...');
        try {
            $user = User::first();
            $ad = Ad::first();
            $package = PaymentPackage::active()->first();
            
            $this->line('   Používateľ: ' . ($user ? "✓ {$user->name}" : '✗ Žiadni používatelia'));
            $this->line('   Inzerát: ' . ($ad ? "✓ ID {$ad->id}" : '✗ Žiadne inzeráty'));
            $this->line('   Balíček: ' . ($package ? "✓ {$package->name}" : '✗ Žiadne balíčky'));
            
            if (!$user || !$ad || !$package) {
                $this->error('   Chýbajú potrebné dáta pre test!');
                return;
            }
            
        } catch (\Exception $e) {
            $this->error('   CHYBA DB: ' . $e->getMessage());
            return;
        }
        $this->newLine();

        // Test 3: Vytvorenie testovej platby
        $this->info('3. Vytvorenie testovej platby...');
        try {
            $payment = AdPayment::create([
                'user_id' => $user->id,
                'ad_id' => $ad->id,
                'payment_package_id' => $package->id,
                'payment_id' => 'TEST-' . time(),
                'amount' => $package->price,
                'currency' => 'EUR',
                'payment_method' => 'stripe',
                'status' => 'pending',
                'duration_days' => $package->duration_days,
                'is_featured' => $package->is_featured,
                'is_top_ad' => $package->is_top_ad,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Command',
                'metadata' => ['test' => true]
            ]);
            
            $this->line("   Platba vytvorená: {$payment->payment_id}");
            
        } catch (\Exception $e) {
            $this->error('   CHYBA pri vytváraní platby: ' . $e->getMessage());
            return;
        }
        $this->newLine();

        // Test 4: Stripe Service test
        $this->info('4. Test Stripe služby...');
        try {
            $stripeService = new StripeService();
            
            // Testové údaje zákazníka
            $customerData = [
                'firstName' => 'Test',
                'lastName' => 'User',
                'email' => 'test@example.com',
                'city' => 'Bratislava',
                'country' => 'SK'
            ];
            
            $result = $stripeService->createPaymentIntent($payment, $customerData);
            
            $this->line('   Stripe API volanie: ' . ($result['success'] ? 'ÚSPECH' : 'CHYBA'));
            
            if ($result['success']) {
                $this->line('   Payment Intent ID: ' . substr($result['payment_intent_id'], 0, 20) . '...');
                $this->line('   Client Secret: ' . substr($result['client_secret'], 0, 20) . '...');
                $this->line('   Publishable Key: ' . substr($result['publishable_key'], 0, 20) . '...');
            } else {
                $this->error('   Chyba: ' . $result['error']);
                if (isset($result['error_code'])) {
                    $this->error('   Error Code: ' . $result['error_code']);
                }
            }
            
        } catch (\Exception $e) {
            $this->error('   CHYBA Stripe Service: ' . $e->getMessage());
            $this->error('   Stack trace: ' . $e->getTraceAsString());
        }
        $this->newLine();

        // Test 5: Vyčistenie
        $this->info('5. Vyčistenie testových dát...');
        try {
            $payment->delete();
            $this->line('   Testová platba vymazaná');
        } catch (\Exception $e) {
            $this->error('   CHYBA pri vymazávaní: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('=== KONIEC TESTU ===');
    }
} 