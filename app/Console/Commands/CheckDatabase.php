<?php

namespace App\Console\Commands;

use App\Models\PaymentPackage;
use App\Models\User;
use App\Models\Ad;
use App\Models\AdPayment;
use Illuminate\Console\Command;

class CheckDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'check:database';

    /**
     * The console command description.
     */
    protected $description = 'Skontroluje stav databázy a zobrazí informácie o kľúčových tabuľkách';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== KONTROLA DATABÁZY ===');

        $this->line('');
        $this->info('Používatelia:');
        $this->line('- Celkom: ' . User::count());
        $this->line('- Admini: ' . User::where('is_admin', true)->count());

        $this->line('');
        $this->info('Platobné balíčky:');
        $packageCount = PaymentPackage::count();
        $this->line('- Celkom: ' . $packageCount);
        $this->line('- Aktívne: ' . PaymentPackage::where('is_active', true)->count());
        $this->line('- Classic: ' . PaymentPackage::where('type', 'classic')->count());
        $this->line('- Premium: ' . PaymentPackage::where('type', 'premium')->count());

        if ($packageCount === 0) {
            $this->warn('POZOR: Žiadne platobné balíčky v databáze!');
            $this->line('Spustite: php artisan seed:payment-packages');
        } else {
            $this->info('Zoznam balíčkov:');
            $packages = PaymentPackage::orderBy('sort_order')->get();
            foreach ($packages as $package) {
                $status = $package->is_active ? '✓' : '✗';
                $this->line("  {$status} {$package->name} ({$package->type}) - {$package->price}€ / {$package->duration_days} dní");
            }
        }

        $this->line('');
        $this->info('Inzeráty:');
        $this->line('- Celkom: ' . Ad::count());
        $this->line('- Aktívne: ' . Ad::where('status', 'active')->count());
        $this->line('- Draft: ' . Ad::where('status', 'draft')->count());
        $this->line('- S aktívnym predplatným: ' . Ad::where('subscription_status', 'active')->count());

        $this->line('');
        $this->info('Platby:');
        $this->line('- Celkom: ' . AdPayment::count());
        $this->line('- Dokončené: ' . AdPayment::where('status', 'completed')->count());
        $this->line('- Čakajúce: ' . AdPayment::where('status', 'pending')->count());
        $this->line('- Neúspešné: ' . AdPayment::where('status', 'failed')->count());

        $this->line('');
        $this->info('Databázové tabuľky:');
        
        try {
            $tables = [
                'users',
                'payment_packages',
                'ads',
                'ad_payments',
                'invoices',
                'notifications',
                'sms_verifications',
                'email_logs'
            ];

            foreach ($tables as $table) {
                try {
                    $count = \DB::table($table)->count();
                    $this->line("- {$table}: {$count} záznamov");
                } catch (\Exception $e) {
                    $this->error("- {$table}: CHYBA - {$e->getMessage()}");
                }
            }
        } catch (\Exception $e) {
            $this->error('Chyba pri kontrole tabuliek: ' . $e->getMessage());
        }

        $this->line('');
        
        if ($packageCount === 0) {
            $this->warn('POTREBNÉ AKCIE:');
            $this->line('1. Spustite: php artisan seed:payment-packages');
            $this->line('2. Alebo: php artisan db:seed --class=PaymentPackageSeeder');
        } else {
            $this->info('Databáza vyzerá v poriadku!');
        }
    }
} 