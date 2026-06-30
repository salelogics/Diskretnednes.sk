<?php

namespace App\Console\Commands;

use App\Models\PaymentPackage;
use Illuminate\Console\Command;

class SeedPaymentPackages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'seed:payment-packages {--fresh : Vymaže existujúce balíčky a vytvorí nové}';

    /**
     * The console command description.
     */
    protected $description = 'Seed payment packages do databázy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Vymazávam existujúce platobné balíčky...');
            
            // Nemôžeme použiť truncate kvôli foreign key constraint
            // Použijeme delete namiesto toho
            $deleted = PaymentPackage::query()->delete();
            $this->line("Vymazané: {$deleted} balíčkov");
        }

        $this->info('Vytváram platobné balíčky...');

        $packages = [
            // Testovací balíček
            [
                'name' => 'Test 1 deň',
                'type' => 'classic',
                'duration_days' => 1,
                'price' => 5.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Testovací balíček za 5€ na 1 deň',
                'features' => [
                    'Lacné testovanie funkcií',
                    'Aktivácia na 1 deň',
                    'Ideálne pre vyskúšanie systému'
                ],
                'is_active' => true,
                'sort_order' => 0
            ],
            // Classic balíčky
            [
                'name' => 'Classic 5 dní',
                'type' => 'classic',
                'duration_days' => 5,
                'price' => 10.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Štandardné zobrazenie v zozname podľa poradia',
                'features' => [
                    'Dostupný cenový balíček',
                    'Štandardné zobrazenie v zozname podľa poradia',
                    'Viditeľný pre všetkých používateľov stránky bez dodatočného zvýraznenia'
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Classic 7 dní',
                'type' => 'classic',
                'duration_days' => 7,
                'price' => 13.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Štandardné zobrazenie v zozname podľa poradia',
                'features' => [
                    'Dostupný cenový balíček',
                    'Štandardné zobrazenie v zozname podľa poradia',
                    'Viditeľný pre všetkých používateľov stránky bez dodatočného zvýraznenia'
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Classic 30 dní',
                'type' => 'classic',
                'duration_days' => 30,
                'price' => 25.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Štandardné zobrazenie v zozname podľa poradia',
                'features' => [
                    'Dostupný cenový balíček',
                    'Štandardné zobrazenie v zozname podľa poradia',
                    'Viditeľný pre všetkých používateľov stránky bez dodatočného zvýraznenia'
                ],
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Classic 90 dní',
                'type' => 'classic',
                'duration_days' => 90,
                'price' => 70.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Štandardné zobrazenie v zozname podľa poradia',
                'features' => [
                    'Dostupný cenový balíček',
                    'Štandardné zobrazenie v zozname podľa poradia',
                    'Viditeľný pre všetkých používateľov stránky bez dodatočného zvýraznenia'
                ],
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Classic 365 dní',
                'type' => 'classic',
                'duration_days' => 365,
                'price' => 200.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Štandardné zobrazenie v zozname podľa poradia',
                'features' => [
                    'Dostupný cenový balíček',
                    'Štandardné zobrazenie v zozname podľa poradia',
                    'Viditeľný pre všetkých používateľov stránky bez dodatočného zvýraznenia'
                ],
                'is_active' => true,
                'sort_order' => 5
            ],
            // Premium balíčky
            [
                'name' => 'Premium 5 dní',
                'type' => 'premium',
                'duration_days' => 5,
                'price' => 20.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                'features' => [
                    'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                    'Zvýraznený a označený ako TOPovaný, čo zaručuje, že ho uvidí viac návštevníkov',
                    'Ideálny pre tých, ktorí chcú rýchlo a efektívne zaujať'
                ],
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Premium 7 dní',
                'type' => 'premium',
                'duration_days' => 7,
                'price' => 25.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                'features' => [
                    'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                    'Zvýraznený a označený ako TOPovaný, čo zaručuje, že ho uvidí viac návštevníkov',
                    'Ideálny pre tých, ktorí chcú rýchlo a efektívne zaujať'
                ],
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Premium 30 dní',
                'type' => 'premium',
                'duration_days' => 30,
                'price' => 40.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                'features' => [
                    'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                    'Zvýraznený a označený ako TOPovaný, čo zaručuje, že ho uvidí viac návštevníkov',
                    'Ideálny pre tých, ktorí chcú rýchlo a efektívne zaujať'
                ],
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'name' => 'Premium 90 dní',
                'type' => 'premium',
                'duration_days' => 90,
                'price' => 90.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                'features' => [
                    'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                    'Zvýraznený a označený ako TOPovaný, čo zaručuje, že ho uvidí viac návštevníkov',
                    'Ideálny pre tých, ktorí chcú rýchlo a efektívne zaujať'
                ],
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'name' => 'Premium 365 dní',
                'type' => 'premium',
                'duration_days' => 365,
                'price' => 240.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                'features' => [
                    'Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania',
                    'Zvýraznený a označený ako TOPovaný, čo zaručuje, že ho uvidí viac návštevníkov',
                    'Ideálny pre tých, ktorí chcú rýchlo a efektívne zaujať'
                ],
                'is_active' => true,
                'sort_order' => 10
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($packages as $packageData) {
            $package = PaymentPackage::where('name', $packageData['name'])->first();
            
            if ($package) {
                $package->update($packageData);
                $updated++;
                $this->line("Aktualizovaný: {$packageData['name']}");
            } else {
                PaymentPackage::create($packageData);
                $created++;
                $this->line("Vytvorený: {$packageData['name']}");
            }
        }

        $this->info("Hotovo! Vytvorené: {$created}, Aktualizované: {$updated}");
        $this->info("Celkom balíčkov v databáze: " . PaymentPackage::count());
    }
} 