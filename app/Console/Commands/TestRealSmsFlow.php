<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SmsPaymentController;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Http\Request;

class TestRealSmsFlow extends Command
{
    protected $signature = 'test:real-sms {phone=0903123456}';
    protected $description = 'Testuje reálny SMS flow od prijatia SMS po aktiváciu';

    public function handle()
    {
        $phone = $this->argument('phone');
        
        $this->info('🧪 TESTOVANIE REÁLNEHO SMS FLOW');
        $this->info('================================');
        $this->newLine();
        
        // 1. Simulujeme prichádzajúcu SMS
        $this->info("📱 1. Simulujeme SMS: ERO FXO");
        $this->info("   Telefón: +421{$phone}");
        
        $request = new Request();
        $request->merge([
            'msisdn' => "+421{$phone}",
            'text' => 'ERO FXO',
            'id' => 'SMS' . time()
        ]);
        
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        
        $controller = app(SmsPaymentController::class);
        $response = $controller->receiveSms($request);
        
        $this->info("   SMS Response: " . $response->getContent());
        $this->newLine();
        
        // 2. Skontrolujeme cache
        $this->info('📋 2. Kontrolujeme cache po SMS:');
        
        $foundCodes = [];
        for ($i = 100000; $i <= 999999; $i++) {
            $key = 'sms_verification_' . str_pad($i, 6, '0', STR_PAD_LEFT);
            if (cache()->has($key)) {
                $data = cache()->get($key);
                $code = str_pad($i, 6, '0', STR_PAD_LEFT);
                $foundCodes[] = $code;
                
                $this->info("   ✅ Nájdený kód: {$code}");
                $this->info("   📞 Telefón: {$data['phone']}");
                $this->info("   📦 Balík: {$data['package']['code']}");
                $this->info("   ⏰ Expiruje: {$data['expires_at']}");
                $this->newLine();
                break; // Stačí nám jeden
            }
        }
        
        if (empty($foundCodes)) {
            $this->error('   ❌ Žiadne verifikačné kódy v cache!');
            return;
        }
        
        // 3. Vytvoríme test inzerát
        $this->info('📝 3. Vytvárame test inzerát:');
        
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'SMS Test User',
                'email' => 'sms-test@diskretnednes.sk',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]);
            $this->info("   ✅ Vytvorený user ID: {$user->id}");
        }
        
        $ad = Ad::create([
            'user_id' => $user->id,
            'nickname' => 'Test SMS Inzerát',
            'ad_type' => 'zena',
            'nationality' => 'SK',
            'age' => 25,
            'phone' => $phone, // Použijeme normalizované číslo
            'city' => 'bratislava',
            'offer_type' => ['ponukam-escort'],
            'girl_selection' => 'som-uplne-sama',
            'experience' => 'skusena',
            'description' => 'Test inzerát pre reálne SMS',
            'status' => 'draft',
            'subscription_status' => 'inactive'
        ]);
        
        $this->info("   ✅ Vytvorený inzerát ID: {$ad->id}");
        $this->info("   📞 Telefón: {$ad->phone}");
        $this->newLine();
        
        // 4. Testujeme verifikáciu s reálnym kódom
        $verificationCode = $foundCodes[0];
        $this->info("🔑 4. Testujeme verifikáciu s reálnym kódom:");
        $this->info("   Kód: {$verificationCode}");
        
        $verifyRequest = new Request();
        $verifyRequest->merge(['verification_code' => $verificationCode]);
        
        try {
            $verifyResponse = $controller->verifyCode($verifyRequest);
            $responseData = json_decode($verifyResponse->getContent(), true);
            
            $this->info("   Response:");
            $this->info("   " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Skontrolujeme stav inzerátu
            $ad->refresh();
            $this->info("   Inzerát status po verifikácii: {$ad->status}");
            $this->info("   Subscription status: {$ad->subscription_status}");
            
            if ($responseData['success'] ?? false) {
                $this->info('🎉 REÁLNY SMS FLOW FUNGUJE!');
            } else {
                $this->error('❌ Verifikácia zlyhala!');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri verifikácii: ' . $e->getMessage());
        }
        
        $this->newLine();
        $this->info('✅ Test dokončený!');
    }
} 