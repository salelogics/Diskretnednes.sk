<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ad;
use App\Http\Controllers\SmsPaymentController;
use Illuminate\Http\Request;

class TestSessionSms extends Command
{
    protected $signature = 'test:session-sms {--ad-id=} {--user-id=}';
    protected $description = 'Testuje session-based SMS aktiváciu';

    public function handle()
    {
        $adId = $this->option('ad-id');
        $userId = $this->option('user-id');
        
        $this->info('🧪 TESTOVANIE SESSION-BASED SMS AKTIVÁCIE');
        $this->info('=============================================');
        $this->newLine();
        
        // Nájdeme alebo vytvoríme test data
        if (!$userId) {
            $user = User::first();
            if (!$user) {
                $user = User::create([
                    'name' => 'SMS Test User',
                    'email' => 'sms-session@test.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now()
                ]);
                $this->info("✅ Vytvorený test user ID: {$user->id}");
            }
            $userId = $user->id;
        }
        
        if (!$adId) {
            $user = User::find($userId);
            $ad = Ad::create([
                'user_id' => $userId,
                'nickname' => 'Session Test Inzerát',
                'ad_type' => 'zena',
                'nationality' => 'SK',
                'age' => 25,
                'phone' => '0905905905', // Tvoje číslo
                'city' => 'bratislava',
                'offer_type' => ['ponukam-escort'],
                'girl_selection' => 'som-uplne-sama',
                'experience' => 'skusena',
                'description' => 'Test inzerát pre session-based SMS',
                'status' => 'draft',
                'subscription_status' => 'inactive'
            ]);
            $this->info("✅ Vytvorený test inzerát ID: {$ad->id}");
            $adId = $ad->id;
        }
        
        $this->info("📋 Test parametre:");
        $this->info("   User ID: {$userId}");
        $this->info("   Ad ID: {$adId}");
        $this->newLine();
        
        // 1. Spustíme session aktiváciu
        $this->info('📱 1. Spúšťam session-based aktiváciu...');
        
        $request = new Request();
        $request->merge(['ad_id' => $adId]);
        
        // Simulujeme auth user
        auth()->loginUsingId($userId);
        
        $controller = app(SmsPaymentController::class);
        $startResponse = $controller->startSmsActivation($request);
        $startData = json_decode($startResponse->getContent(), true);
        
        $this->info("   Response: " . json_encode($startData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->newLine();
        
        // 2. Simulujeme SMS príjem
        $this->info('📨 2. Simulujem príjem SMS "ERO FXO"...');
        
        $smsRequest = new Request();
        $smsRequest->merge([
            'msisdn' => '+4210915967510', // Číslo z ktorého prišla SMS
            'text' => 'ERO FXO',
            'id' => 'SMS' . time()
        ]);
        $smsRequest->server->set('REMOTE_ADDR', '127.0.0.1');
        
        $smsResponse = $controller->receiveSms($smsRequest);
        $this->info("   SMS Response: " . $smsResponse->getContent());
        $this->newLine();
        
        // 3. Nájdeme verifikačný kód
        $this->info('🔍 3. Hľadám verifikačný kód v cache...');
        $foundCode = null;
        
        for ($i = 100000; $i <= 999999; $i++) {
            $key = 'sms_verification_' . str_pad($i, 6, '0', STR_PAD_LEFT);
            if (cache()->has($key)) {
                $foundCode = str_pad($i, 6, '0', STR_PAD_LEFT);
                $this->info("   ✅ Nájdený kód: {$foundCode}");
                break;
            }
        }
        
        if (!$foundCode) {
            $this->error('   ❌ Žiadny verifikačný kód nebol nájdený!');
            return;
        }
        
        // 4. Testujeme verifikáciu s session context
        $this->info('🔑 4. Testujem verifikáciu s session context...');
        
        $verifyRequest = new Request();
        $verifyRequest->merge(['verification_code' => $foundCode]);
        
        $verifyResponse = $controller->verifyCode($verifyRequest);
        $verifyData = json_decode($verifyResponse->getContent(), true);
        
        $this->info("   Response:");
        $this->info("   " . json_encode($verifyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->newLine();
        
        // 5. Skontrolujeme výsledok
        $ad = Ad::find($adId);
        $this->info('📊 5. Finálny stav inzerátu:');
        $this->info("   Status: {$ad->status}");
        $this->info("   Subscription: {$ad->subscription_status}");
        $this->info("   Expires: " . ($ad->subscription_expires_at ? $ad->subscription_expires_at->format('Y-m-d H:i:s') : 'N/A'));
        
        if ($verifyData['success'] ?? false) {
            $this->info('🎉 SESSION-BASED SMS AKTIVÁCIA FUNGUJE!');
            $this->info('   ✅ Inzerát 0905905905 bol aktivovaný SMS z 0915967510');
        } else {
            $this->error('❌ Session-based aktivácia zlyhala!');
        }
    }
} 