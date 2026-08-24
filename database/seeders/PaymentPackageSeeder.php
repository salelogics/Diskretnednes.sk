<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentPackage;

class PaymentPackageSeeder extends Seeder
{
    public function run()
    {
        // Zmažeme existujúce balíčky (soft delete kvôli foreign keys)
        PaymentPackage::query()->delete();

        // Classic - zadarmo, bez časového limitu
        PaymentPackage::create([
            'name' => 'Classic',
            'type' => 'classic',
            'duration_days' => 0,
            'price' => 0.00,
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu zadarmo, bez časového limitu',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia', 'Bez časového limitu'],
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Premium topované balíčky
        PaymentPackage::create([
            'name' => 'Premium 10 dní',
            'type' => 'premium',
            'duration_days' => 10,
            'price' => 10.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 10 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita vo vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 2
        ]);

        PaymentPackage::create([
            'name' => 'Premium 30 dní',
            'type' => 'premium',
            'duration_days' => 30,
            'price' => 20.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 30 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita vo vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 3
        ]);

        $this->command->info('Vytvorených ' . PaymentPackage::count() . ' platobných balíčkov.');
    }
}
