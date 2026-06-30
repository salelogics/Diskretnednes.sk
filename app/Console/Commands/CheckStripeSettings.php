<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class CheckStripeSettings extends Command
{
    protected $signature = 'stripe:check-settings';
    protected $description = 'Check Stripe settings from admin panel';

    public function handle()
    {
        $this->info('=== STRIPE ADMIN NASTAVENIA ===');
        $this->newLine();

        // Načítame nastavenia z databázy
        $stripePublishableKey = Setting::get('stripe_publishable_key');
        $stripeSecretKey = Setting::get('stripe_secret_key');
        $stripeWebhookSecret = Setting::get('stripe_webhook_secret');

        $this->info('Nastavenia z administrácie:');
        $this->line('   Stripe Publishable Key: ' . ($stripePublishableKey ? 'NASTAVENÝ (' . substr($stripePublishableKey, 0, 12) . '...)' : 'CHÝBA'));
        $this->line('   Stripe Secret Key: ' . ($stripeSecretKey ? 'NASTAVENÝ (' . substr($stripeSecretKey, 0, 12) . '...)' : 'CHÝBA'));
        $this->line('   Stripe Webhook Secret: ' . ($stripeWebhookSecret ? 'NASTAVENÝ (' . substr($stripeWebhookSecret, 0, 12) . '...)' : 'CHÝBA'));
        $this->newLine();

        // Načítame nastavenia z .env súboru
        $envStripeKey = config('services.stripe.key');
        $envStripeSecret = config('services.stripe.secret');
        $envWebhookSecret = config('services.stripe.webhook_secret');

        $this->info('Nastavenia z .env súboru:');
        $this->line('   STRIPE_KEY: ' . ($envStripeKey ? 'NASTAVENÝ (' . substr($envStripeKey, 0, 12) . '...)' : 'CHÝBA'));
        $this->line('   STRIPE_SECRET: ' . ($envStripeSecret ? 'NASTAVENÝ (' . substr($envStripeSecret, 0, 12) . '...)' : 'CHÝBA'));
        $this->line('   STRIPE_WEBHOOK_SECRET: ' . ($envWebhookSecret ? 'NASTAVENÝ (' . substr($envWebhookSecret, 0, 12) . '...)' : 'CHÝBA'));
        $this->newLine();

        // Porovnanie
        $this->info('Porovnanie:');
        if ($stripePublishableKey && $envStripeKey) {
            $match = $stripePublishableKey === $envStripeKey;
            $this->line('   Publishable Key: ' . ($match ? '✓ ZHODUJÚ SA' : '✗ NEZHODUJÚ SA'));
        }

        if ($stripeSecretKey && $envStripeSecret) {
            $match = $stripeSecretKey === $envStripeSecret;
            $this->line('   Secret Key: ' . ($match ? '✓ ZHODUJÚ SA' : '✗ NEZHODUJÚ SA'));
        }
        $this->newLine();

        // Odporúčania
        $this->info('Odporúčania:');
        if (!$stripePublishableKey || !$stripeSecretKey) {
            $this->warn('   → Nastavte Stripe kľúče v administrácii (Nastavenia → Platby)');
        }
        
        if (!$envStripeKey || !$envStripeSecret) {
            $this->warn('   → Nastavte STRIPE_KEY a STRIPE_SECRET v .env súbore');
        }

        if ($stripePublishableKey && $envStripeKey && $stripePublishableKey !== $envStripeKey) {
            $this->warn('   → Admin panel a .env majú rôzne kľúče - používa sa .env');
        }
        
        // Kontrola platnosti kľúčov
        if ($envStripeKey && $envStripeSecret) {
            if (str_starts_with($envStripeKey, 'pk_test_') && str_starts_with($envStripeSecret, 'sk_test_')) {
                $this->info('   → Používate TESTOVÉ kľúče (vhodné pre development)');
            } elseif (str_starts_with($envStripeKey, 'pk_live_') && str_starts_with($envStripeSecret, 'sk_live_')) {
                $this->info('   → Používate PRODUKČNÉ kľúče (vhodné pre live server)');
            } else {
                $this->error('   → NEKONZISTENTNÉ kľúče (mix test/live alebo neplatné)');
            }
        }

        $this->newLine();
        $this->info('=== KONIEC KONTROLY ===');
    }
} 