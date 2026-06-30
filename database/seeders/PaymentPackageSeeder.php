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

        // Classic balíčky
        PaymentPackage::create([
            'name' => 'Classic 1 deň',
            'type' => 'classic',
            'duration_days' => 1,
            'price' => 5.00, // Upravená testovacia cena
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu na 1 deň',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia'],
            'is_active' => true,
            'sort_order' => 1
        ]);

        PaymentPackage::create([
            'name' => 'Classic 5 dní',
            'type' => 'classic',
            'duration_days' => 5,
            'price' => 10.00,
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu na 5 dní',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia'],
            'is_active' => true,
            'sort_order' => 2
        ]);

        PaymentPackage::create([
            'name' => 'Classic 7 dní',
            'type' => 'classic',
            'duration_days' => 7,
            'price' => 13.00,
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu na 7 dní',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia'],
            'is_active' => true,
            'sort_order' => 3
        ]);

        PaymentPackage::create([
            'name' => 'Classic 30 dní',
            'type' => 'classic',
            'duration_days' => 30,
            'price' => 25.00,
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu na 30 dní',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia'],
            'is_active' => true,
            'sort_order' => 4
        ]);

        PaymentPackage::create([
            'name' => 'Classic 90 dní',
            'type' => 'classic',
            'duration_days' => 90,
            'price' => 70.00,
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu na 90 dní',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia'],
            'is_active' => true,
            'sort_order' => 5
        ]);

        PaymentPackage::create([
            'name' => 'Classic 365 dní',
            'type' => 'classic',
            'duration_days' => 365,
            'price' => 200.00,
            'is_featured' => false,
            'is_top_ad' => false,
            'description' => 'Základné zobrazenie inzerátu na 365 dní',
            'features' => ['Základné zobrazenie', 'Štandardná pozícia'],
            'is_active' => true,
            'sort_order' => 6
        ]);

        // Premium balíčky
        PaymentPackage::create([
            'name' => 'Premium 1 deň',
            'type' => 'premium',
            'duration_days' => 1,
            'price' => 5.00, // Upravená testovacia cena
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 1 deň',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita v vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 7
        ]);

        PaymentPackage::create([
            'name' => 'Premium 5 dní',
            'type' => 'premium',
            'duration_days' => 5,
            'price' => 20.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 5 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita v vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 8
        ]);

        PaymentPackage::create([
            'name' => 'Premium 7 dní',
            'type' => 'premium',
            'duration_days' => 7,
            'price' => 25.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 7 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita v vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 9
        ]);

        PaymentPackage::create([
            'name' => 'Premium 30 dní',
            'type' => 'premium',
            'duration_days' => 30,
            'price' => 40.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 30 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita v vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 10
        ]);

        PaymentPackage::create([
            'name' => 'Premium 90 dní',
            'type' => 'premium',
            'duration_days' => 90,
            'price' => 90.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 90 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita v vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 11
        ]);

        PaymentPackage::create([
            'name' => 'Premium 365 dní',
            'type' => 'premium',
            'duration_days' => 365,
            'price' => 240.00,
            'is_featured' => true,
            'is_top_ad' => true,
            'description' => 'Topované zobrazenie inzerátu na 365 dní',
            'features' => ['Topované zobrazenie', 'Zvýraznenie', 'Priorita v vyhľadávaní'],
            'is_active' => true,
            'sort_order' => 12
        ]);

        $this->command->info('Vytvorených ' . PaymentPackage::count() . ' platobných balíčkov.');
    }
}
