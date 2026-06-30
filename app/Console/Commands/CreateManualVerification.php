<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use App\Services\SmsPaymentService;
use App\Http\Controllers\SmsPaymentController;
use Illuminate\Http\Request;

class CreateManualVerification extends Command
{
    protected $signature = 'test:manual-sms {--code=FXO} {--phone=0903123456}';
    protected $description = 'Manuálne vytvorí verifikáciu a testuje aktiváciu bez HTTP';

    public function handle()
    {
        $smsCode = $this->option('code');
        $phone = $this->option('phone');
        
        $this->info("🔧 Manuálne testovanie SMS aktivácie");
        $this->line("SMS kód: {$smsCode}");
        $this->line("Telefón: {$phone}");
        $this->newLine();
        
        // 1. Skontroluj inzerát
        $ad = Ad::where('phone', $phone)->first();
        if (!$ad) {
            $this->error("❌ Inzerát s telefónom {$phone} neexistuje!");
            return 1;
        }
        
        $this->info("✅ Inzerát nájdený (ID: {$ad->id})");
        $this->line("   Status pred aktiváciou: {$ad->status}");
        $this->line("   Subscription pred aktiváciou: {$ad->subscription_status}");
        $this->newLine();
        
        // 2. Získaj SMS balík (alebo použij testovací)
        if (in_array($smsCode, ['999888', '123456'])) {
            $package = [
                'code' => $smsCode,
                'type' => 'premium',
                'duration' => 1,
                'price' => 7.00,
                'description' => $smsCode . ' Premium balík (testovací)'
            ];
        } else {
            $smsService = app(SmsPaymentService::class);
            $smsPackages = $smsService->getSmsPackages();
            $package = collect($smsPackages)->firstWhere('code', $smsCode);
            
            if (!$package) {
                $this->error("❌ SMS kód {$smsCode} neexistuje!");
                return 1;
            }
        }
        
        $this->info("✅ SMS balík: {$package['description']} ({$package['price']}€)");
        $this->newLine();
        
        // 3. Vytvor verifikáciu v cache
        $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'sms_verification_' . $verificationCode;
        
        $verificationData = [
            'phone' => $phone,
            'package' => $package,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(15)
        ];
        
        cache()->put($cacheKey, $verificationData, 900);
        
        $this->info("✅ Verifikácia vytvorená v cache");
        $this->line("   Kód: {$verificationCode}");
        $this->line("   Cache kľúč: {$cacheKey}");
        $this->newLine();
        
        // 4. Simuluj verifikáciu (volaj controller priamo)
        $controller = app(SmsPaymentController::class);
        
        // Vytvoríme fake request
        $request = new Request();
        $request->merge(['verification_code' => $verificationCode]);
        
        try {
            $this->info("🚀 Spúšťam verifikáciu...");
            
            $response = $controller->verifyCode($request);
            $responseData = $response->getData(true);
            
            $this->line("   HTTP Status: {$response->getStatusCode()}");
            $this->line("   Response: " . json_encode($responseData, JSON_PRETTY_PRINT));
            $this->newLine();
            
            // 5. Skontroluj stav inzerátu po aktivácii
            $ad->refresh();
            
            $this->info("📊 Stav inzerátu po aktivácii:");
            $this->line("   Status: {$ad->status}");
            $this->line("   Subscription: {$ad->subscription_status}");
            $this->line("   Expires: " . ($ad->subscription_expires_at ?? 'Nenastavené'));
            $this->newLine();
            
            // 6. Vyhodnotenie
            if ($responseData['success'] ?? false) {
                if ($ad->status === 'active' && $ad->subscription_status === 'active') {
                    $this->info("🎉 SMS AKTIVÁCIA ÚSPEŠNÁ!");
                } else {
                    $this->error("❌ Verifikácia prošla, ale inzerát nie je aktivovaný");
                    $this->line("   Očakávané: status=active, subscription=active");
                    $this->line("   Skutočné: status={$ad->status}, subscription={$ad->subscription_status}");
                }
            } else {
                $this->error("❌ Verifikácia zlyhala: " . ($responseData['message'] ?? 'Neznáma chyba'));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Chyba pri verifikácii: " . $e->getMessage());
            $this->line("   Trace: " . $e->getTraceAsString());
            
            // Aj tak skontroluj stav inzerátu
            $ad->refresh();
            $this->newLine();
            $this->info("📊 Stav inzerátu po chybe:");
            $this->line("   Status: {$ad->status}");
            $this->line("   Subscription: {$ad->subscription_status}");
        }
        
        return 0;
    }
} 