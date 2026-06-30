<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentPackage;
use Illuminate\Support\Facades\DB;

class SeedSmsPackages extends Command
{
    protected $signature = 'seed:sms-packages';
    protected $description = 'Naseeduje PaymentPackage balíky ak neexistujú';

    public function handle()
    {
        $this->info('Kontrolujem PaymentPackage balíky...');
        
        // Skontrolujeme existujúce balíky
        $existingCount = PaymentPackage::count();
        $this->line("Aktuálne existuje {$existingCount} balíkov v databáze");
        
        if ($existingCount > 0) {
            $existing = PaymentPackage::all(['id', 'name', 'type', 'duration_days', 'price']);
            $this->table(
                ['ID', 'Názov', 'Typ', 'Dni', 'Cena'],
                $existing->map(fn($p) => [$p->id, $p->name, $p->type, $p->duration_days, $p->price . '€'])->toArray()
            );
            
            if (!$this->confirm('Chcete reinicializovať balíky?')) {
                $this->info('Zrušené.');
                return 0;
            }
        }
        
        $this->info('Vytváram SMS kompatibilné balíky...');
        
        DB::transaction(function() {
            // Testovací balíček (SMS FXO)
            PaymentPackage::updateOrCreate(
                ['type' => 'classic', 'duration_days' => 1],
                [
                    'name' => 'Classic 1 deň',
                    'price' => 5.00,
                    'is_featured' => false,
                    'is_top_ad' => false,
                    'description' => 'SMS balík FXO - 1 deň za 5€',
                    'features' => ['Základné zobrazenie', 'SMS aktivácia', 'Testovacia cena'],
                    'is_active' => true,
                    'sort_order' => 1
                ]
            );
            
            // Classic 2 dni (SMS 9E8)
            PaymentPackage::updateOrCreate(
                ['type' => 'classic', 'duration_days' => 2],
                [
                    'name' => 'Classic 2 dni',
                    'price' => 7.00,
                    'is_featured' => false,
                    'is_top_ad' => false,
                    'description' => 'SMS balík 9E8 - 2 dni za 7€',
                    'features' => ['Základné zobrazenie', 'SMS aktivácia'],
                    'is_active' => true,
                    'sort_order' => 2
                ]
            );
            
            // Classic 3 dni (SMS LS2)
            PaymentPackage::updateOrCreate(
                ['type' => 'classic', 'duration_days' => 3],
                [
                    'name' => 'Classic 3 dni',
                    'price' => 9.00,
                    'is_featured' => false,
                    'is_top_ad' => false,
                    'description' => 'SMS balík LS2 - 3 dni za 9€',
                    'features' => ['Základné zobrazenie', 'SMS aktivácia'],
                    'is_active' => true,
                    'sort_order' => 3
                ]
            );
            
            // Classic 5 dní (SMS 8E2)
            PaymentPackage::updateOrCreate(
                ['type' => 'classic', 'duration_days' => 5],
                [
                    'name' => 'Classic 5 dní',
                    'price' => 10.00,
                    'is_featured' => false,
                    'is_top_ad' => false,
                    'description' => 'SMS balík 8E2 - 5 dní za 10€',
                    'features' => ['Základné zobrazenie', 'SMS aktivácia'],
                    'is_active' => true,
                    'sort_order' => 4
                ]
            );
            
            // Premium balíky
            PaymentPackage::updateOrCreate(
                ['type' => 'premium', 'duration_days' => 1],
                [
                    'name' => 'Premium 1 deň',
                    'price' => 7.00,
                    'is_featured' => true,
                    'is_top_ad' => true,
                    'description' => 'SMS balík CC3 - Premium 1 deň za 7€',
                    'features' => ['Topované zobrazenie', 'SMS aktivácia', 'Priorita'],
                    'is_active' => true,
                    'sort_order' => 10
                ]
            );
            
            PaymentPackage::updateOrCreate(
                ['type' => 'premium', 'duration_days' => 2],
                [
                    'name' => 'Premium 2 dni',
                    'price' => 10.00,
                    'is_featured' => true,
                    'is_top_ad' => true,
                    'description' => 'SMS balík Y7Q - Premium 2 dni za 10€',
                    'features' => ['Topované zobrazenie', 'SMS aktivácia', 'Priorita'],
                    'is_active' => true,
                    'sort_order' => 11
                ]
            );
        });
        
        $finalCount = PaymentPackage::count();
        $this->info("✅ Hotovo! Celkovo {$finalCount} balíkov v databáze");
        
        // Ukážeme výsledok
        $this->newLine();
        $this->info('🎯 SMS kompatibilné balíky:');
        $smsPackages = PaymentPackage::whereIn('duration_days', [1, 2, 3, 5])->get();
        $this->table(
            ['Typ', 'Dni', 'Cena', 'SMS kód'],
            $smsPackages->map(function($p) {
                $smsCode = match([$p->type, $p->duration_days]) {
                    ['classic', 1] => 'FXO',
                    ['classic', 2] => '9E8',
                    ['classic', 3] => 'LS2',
                    ['classic', 5] => '8E2',
                    ['premium', 1] => 'CC3',
                    ['premium', 2] => 'Y7Q',
                    default => '?'
                };
                return [$p->type, $p->duration_days, $p->price . '€', 'ERO ' . $smsCode];
            })->toArray()
        );
        
        return 0;
    }
} 