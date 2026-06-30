<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ad;
use App\Http\Controllers\SmsPaymentController;
use Illuminate\Http\Request;

class TestAdBasedSms extends Command
{
    protected $signature = 'test:ad-based-sms {--ad-phone=0905905905} {--sms-phone=0915967510}';
    protected $description = 'Testuje nový ad-based SMS systém s rôznymi telefónnymi číslami';

    public function handle()
    {
        $adPhone = $this->option('ad-phone');
        $smsPhone = $this->option('sms-phone');
        
        $this->info('🎯 TESTOVANIE AD-BASED SMS AKTIVÁCIE');
        $this->info('====================================');
        $this->newLine();
        
        $this->info("📋 Test scenár:");
        $this->info("   Inzerát má číslo: {$adPhone}");
        $this->info("   SMS príde z čísla: {$smsPhone}");
        $this->info("   (Rôzne čísla - fallback by zlyhal)");
        $this->newLine();
        
        // 1. Vytvoríme test data
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Ad-Based Test User',
                'email' => 'ad-test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]);
            $this->info("✅ Vytvorený test user ID: {$user->id}");
        }
        
        $ad = Ad::create([
            'user_id' => $user->id,
            'nickname' => 'Ad-Based Test Inzerát',
            'ad_type' => 'zena',
            'nationality' => 'SK',
            'age' => 25,
            'phone' => $adPhone, // Telefón inzerátu
            'city' => 'bratislava',
            'offer_type' => ['ponukam-escort'],
            'girl_selection' => 'som-uplne-sama',
            'experience' => 'skusena',
            'description' => 'Test ad-based SMS aktivácie',
            'status' => 'draft',
            'subscription_status' => 'inactive'
        ]);
        
        $this->info("✅ Vytvorený test inzerát ID: {$ad->id}");
        $this->newLine();
        
        // 2. Spustíme ad-based aktiváciu
        $this->info('🖱️  1. Simulujem klik "Aktivovať SMS" pri inzeráte...');
        
        auth()->loginUsingId($user->id);
        
        $startRequest = new Request();
        $startRequest->merge(['ad_id' => $ad->id]);
        
        $controller = app(SmsPaymentController::class);
        $startResponse = $controller->startSmsActivation($startRequest);
        $startData = json_decode($startResponse->getContent(), true);
        
        $this->info("   Response: " . ($startData['success'] ? '✅ SUCCESS' : '❌ FAILED'));
        $this->info("   Message: " . $startData['message']);
        
        // 3. Overíme cache
        $activationKey = 'ad_sms_activation_' . $ad->id;
        $activationData = cache()->get($activationKey);
        
        $this->info("\n📦 2. Kontrola pending aktivácie v cache:");
        $this->info("   Cache key: {$activationKey}");
        $this->info("   Activation data: " . ($activationData ? '✅ FOUND' : '❌ NOT FOUND'));
        if ($activationData) {
            $this->info("   Ad ID: " . $activationData['ad_id']);
            $this->info("   Status: " . $activationData['status']);
        }
        
        // 4. Simulujeme SMS príjem
        $this->info("\n📱 3. Simulujem SMS 'ERO FXO' z čísla {$smsPhone}...");
        
        $smsRequest = new Request();
        $smsRequest->merge([
            'msisdn' => '+421' . ltrim($smsPhone, '0'), // Konverzia na medzinárodný formát
            'text' => 'ERO FXO',
            'id' => 'SMS' . time()
        ]);
        $smsRequest->server->set('REMOTE_ADDR', '127.0.0.1');
        
        $smsResponse = $controller->receiveSms($smsRequest);
        $this->info("   SMS Response: " . $smsResponse->getContent());
        
        // 5. Nájdeme verifikačný kód
        $this->info("\n🔍 4. Hľadám verifikačný kód v cache...");
        $foundCode = null;
        
        for ($i = 100000; $i <= 999999; $i++) {
            $key = 'sms_verification_' . str_pad($i, 6, '0', STR_PAD_LEFT);
            if (cache()->has($key)) {
                $foundCode = str_pad($i, 6, '0', STR_PAD_LEFT);
                $codeData = cache()->get($key);
                $this->info("   ✅ Nájdený kód: {$foundCode}");
                $this->info("   SMS telefón: " . $codeData['phone']);
                break;
            }
        }
        
        if (!$foundCode) {
            $this->error('   ❌ Žiadny verifikačný kód nebol nájdený!');
            return;
        }
        
        // 6. Testujeme ad-based verifikáciu
        $this->info("\n🔗 5. Testujem ad-based spárovanie...");
        
        $verifyRequest = new Request();
        $verifyRequest->merge(['verification_code' => $foundCode]);
        
        $verifyResponse = $controller->verifyCode($verifyRequest);
        $verifyData = json_decode($verifyResponse->getContent(), true);
        
        $this->info("   Response:");
        $this->info("   " . json_encode($verifyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // 7. Kontrolujeme výsledok
        $ad->refresh();
        
        $this->info("\n📊 6. Finálny stav inzerátu:");
        $this->info("   ID: {$ad->id}");
        $this->info("   Telefón inzerátu: {$ad->phone}");
        $this->info("   Status: {$ad->status}");
        $this->info("   Subscription: {$ad->subscription_status}");
        $this->info("   Expires: " . ($ad->subscription_expires_at ? $ad->subscription_expires_at->format('Y-m-d H:i:s') : 'N/A'));
        
        // 8. Kontrolujeme či sa vyčistil cache
        $this->info("\n🧹 7. Kontrola vyčistenia cache:");
        $activationDataAfter = cache()->get($activationKey);
        $this->info("   Pending aktivácia: " . ($activationDataAfter ? '❌ STÁLE TU' : '✅ VYČISTENÁ'));
        
        $this->newLine();
        if ($verifyData['success'] ?? false) {
            $this->info('🎉 AD-BASED SMS AKTIVÁCIA FUNGUJE PERFEKTNE!');
            $this->info("   ✅ Inzerát {$adPhone} bol aktivovaný SMS z {$smsPhone}");
            $this->info('   ✅ Rôzne telefónne čísla nie sú problém!');
            $this->info('   ✅ Žiadne cookies/session problémy!');
        } else {
            $this->error('❌ Ad-based aktivácia zlyhala!');
        }
    }
} 