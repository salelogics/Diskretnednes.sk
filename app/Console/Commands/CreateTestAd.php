<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ad;

class CreateTestAd extends Command
{
    protected $signature = 'test:create-sms-ad {--phone=0903123456}';
    protected $description = 'Vytvorí testovací inzerát pre SMS aktiváciu';

    public function handle()
    {
        $phone = $this->option('phone');
        
        // Nájdeme alebo vytvoríme test usera
        $user = User::firstOrCreate(
            ['email' => 'sms-test@erotikon.sk'],
            [
                'name' => 'SMS Test User',
                'password' => bcrypt('password123'),
                'email_verified_at' => now()
            ]
        );

        // Skontrolujeme či už existuje inzerát s týmto číslom
        $existingAd = Ad::where('phone', $phone)->first();
        
        if ($existingAd) {
            $this->info("Inzerát s číslom {$phone} už existuje (ID: {$existingAd->id})");
            $this->info("Status: {$existingAd->status}");
            $this->info("Subscription status: {$existingAd->subscription_status}");
            return;
        }

        // Vytvoríme testovací inzerát
        $ad = Ad::create([
            'user_id' => $user->id,
            'nickname' => 'SMS Test Inzerát',
            'ad_type' => 'zena',
            'nationality' => 'SK',
            'age' => 25,
            'phone' => $phone,
            'city' => 'bratislava',
            'offer_type' => ['ponukam-escort'],
            'girl_selection' => 'som-uplne-sama',
            'experience' => 'skusena',
            'description' => 'Testovací inzerát pre SMS aktiváciu. Použite kód 123456.',
            'status' => 'draft', // Začne ako koncept
            'subscription_status' => 'inactive',
            'subscription_expires_at' => null
        ]);

        $this->info("✅ Vytvorený testovací inzerát:");
        $this->line("ID: {$ad->id}");
        $this->line("Telefón: {$ad->phone}");
        $this->line("Status: {$ad->status}");
        $this->line("Subscription: {$ad->subscription_status}");
        $this->newLine();
        $this->info("🔥 Teraz môžete použiť SMS kód: 123456");
        $this->info("📱 Pre telefónne číslo: {$phone}");
        
        return 0;
    }
} 