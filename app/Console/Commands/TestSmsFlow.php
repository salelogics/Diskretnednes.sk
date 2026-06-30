<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use App\Services\SmsPaymentService;
use Illuminate\Support\Facades\Http;

class TestSmsFlow extends Command
{
    protected $signature = 'test:sms-flow {--code=FXO} {--phone=0903123456}';
    protected $description = 'Testuje celý SMS flow od SMS až po aktiváciu';

    public function handle()
    {
        $smsCode = $this->option('code');
        $phone = $this->option('phone');
        
        $this->info("🚀 Testujem SMS flow pre kód: {$smsCode}");
        $this->newLine();
        
        // 1. Skontrolujeme či existuje inzerát
        $ad = Ad::where('phone', $phone)->first();
        if (!$ad) {
            $this->error("❌ Inzerát s telefónom {$phone} neexistuje!");
            $this->line("Spusti: php artisan test:create-sms-ad --phone={$phone}");
            return 1;
        }
        
        $this->info("✅ Inzerát nájdený (ID: {$ad->id})");
        $this->line("   Status: {$ad->status}");
        $this->line("   Subscription: {$ad->subscription_status}");
        $this->newLine();
        
        // 2. Skontrolujeme SMS balíky
        $smsService = app(SmsPaymentService::class);
        $smsPackages = $smsService->getSmsPackages();
        $package = collect($smsPackages)->firstWhere('code', $smsCode);
        
        if (!$package) {
            $this->error("❌ SMS kód {$smsCode} neexistuje!");
            $this->line("Dostupné kódy:");
            foreach ($smsPackages as $pkg) {
                $this->line("   {$pkg['code']} - {$pkg['description']} ({$pkg['price']}€)");
            }
            return 1;
        }
        
        $this->info("✅ SMS balík nájdený: {$package['description']} za {$package['price']}€");
        $this->newLine();
        
        // 3. Vytvoríme verifikáciu (simulujeme SMS)
        $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'sms_verification_' . $verificationCode;
        
        $verificationData = [
            'phone' => $phone,
            'package' => $package,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(15)
        ];
        
        cache()->put($cacheKey, $verificationData, 900);
        
        $this->info("✅ Verifikačný kód vytvorený: {$verificationCode}");
        $this->line("   Exspiruje: {$verificationData['expires_at']}");
        $this->newLine();
        
        // 4. Testujeme verifikáciu cez HTTP
        if ($this->confirm('Chcete automaticky verifikovať kód?', true)) {
            try {
                $response = Http::post(url('/sms/verify'), [
                    'verification_code' => $verificationCode
                ]);
                
                $this->info("📡 HTTP Request odoslaný na /sms/verify");
                $this->line("   Status: {$response->status()}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['success']) {
                        $this->info("✅ Verifikácia úspešná!");
                        
                        // Skontrolujeme stav inzerátu
                        $ad->refresh();
                        $this->newLine();
                        $this->info("📊 Stav inzerátu po aktivácii:");
                        $this->line("   Status: {$ad->status}");
                        $this->line("   Subscription: {$ad->subscription_status}");
                        $this->line("   Expires: " . ($ad->subscription_expires_at ?? 'Nie je nastavené'));
                        
                        if ($ad->status === 'active' && $ad->subscription_status === 'active') {
                            $this->info("🎉 SMS FLOW ÚSPEŠNÝ! Inzerát je aktivovaný!");
                        } else {
                            $this->error("❌ SMS flow neúspešný - inzerát nie je aktivovaný");
                            $this->line("   Očakávané: status=active, subscription_status=active");
                            $this->line("   Skutočné: status={$ad->status}, subscription_status={$ad->subscription_status}");
                        }
                    } else {
                        $this->error("❌ Verifikácia zlyhala: " . ($data['message'] ?? 'Neznáma chyba'));
                    }
                } else {
                    $this->error("❌ HTTP Request zlyhal: {$response->body()}");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Chyba pri HTTP requeste: " . $e->getMessage());
            }
        } else {
            $this->info("🔧 Manuálne testovanie:");
            $this->line("   1. Choď na stránku s overovacím formulárom");
            $this->line("   2. Zadaj kód: {$verificationCode}");
            $this->line("   3. Skontroluj stav inzerátu (ID: {$ad->id})");
        }
        
        $this->newLine();
        $this->info("Debug URLs:");
        $this->line("   🔍 Debug info: " . url("/sms/debug?phone={$phone}"));
        $this->line("   📋 Cache info: " . url("/sms/debug"));
        
        return 0;
    }
} 