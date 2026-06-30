<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class SetStripeKeys extends Command
{
    protected $signature = 'stripe:set-keys {--test : Use test keys} {--live : Use live keys} {--pub= : Custom publishable key} {--secret= : Custom secret key}';
    protected $description = 'Set Stripe keys in admin settings';

    public function handle()
    {
        $this->info('=== NASTAVENIE STRIPE KĽÚČOV ===');
        $this->newLine();

        if ($this->option('test')) {
            // Test kľúče pre development
            $publishableKey = 'pk_test_51234567890abcdefghijklmnopqrstuvwxyz';
            $secretKey = 'sk_test_51234567890abcdefghijklmnopqrstuvwxyz';
            
            $this->info('Nastavujem TESTOVÉ kľúče...');
            
        } elseif ($this->option('live')) {
            // Live kľúče pre produkciu
            $publishableKey = $this->ask('Zadajte live publishable key (pk_live_...)');
            $secretKey = $this->secret('Zadajte live secret key (sk_live_...)');
            
            $this->info('Nastavujem PRODUKČNÉ kľúče...');
            
        } elseif ($this->option('pub') && $this->option('secret')) {
            $publishableKey = $this->option('pub');
            $secretKey = $this->option('secret');
            
            $this->info('Nastavujem vlastné kľúče...');
            
        } else {
            $this->error('Použite jednu z možností:');
            $this->line('  --test          Pre testové kľúče');
            $this->line('  --live          Pre produkčné kľúče (interaktívne)');
            $this->line('  --pub=X --secret=Y  Pre vlastné kľúče');
            return;
        }

        // Validácia kľúčov
        if (!str_starts_with($publishableKey, 'pk_')) {
            $this->error('Publishable key musí začínať "pk_"');
            return;
        }

        if (!str_starts_with($secretKey, 'sk_')) {
            $this->error('Secret key musí začínať "sk_"');
            return;
        }

        // Uloženie do databázy
        try {
            Setting::set('stripe_publishable_key', $publishableKey, 'Stripe Publishable Key');
            Setting::set('stripe_secret_key', $secretKey, 'Stripe Secret Key');
            
            $this->info('✓ Kľúče boli úspešne uložené v admin nastaveniach');
            $this->line('  Publishable Key: ' . substr($publishableKey, 0, 12) . '...');
            $this->line('  Secret Key: ' . substr($secretKey, 0, 12) . '...');
            
        } catch (\Exception $e) {
            $this->error('Chyba pri ukladaní: ' . $e->getMessage());
            return;
        }

        $this->newLine();
        $this->info('Teraz môžete otestovať konfiguráciu:');
        $this->line('  php artisan stripe:test-config');
        $this->line('  php artisan stripe:check-settings');
    }
} 