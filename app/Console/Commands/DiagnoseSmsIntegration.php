<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmsPaymentService;
use App\Models\SmsVerification;

class DiagnoseSmsIntegration extends Command
{
    protected $signature = 'sms:diagnose';
    protected $description = 'Diagnostika SMS integrácie a konfigurácie';

    public function handle()
    {
        $this->info('🔍 DIAGNOSTIKA SMS INTEGRÁCIE');
        $this->info('=====================================');
        
        // 1. Kontrola ENV premenných
        $this->info('📋 ENV konfigurácia:');
        $pid = env('SMS_PAYMENT_PID');
        $secret = env('SMS_PAYMENT_SECRET');
        $url = env('SMS_PAYMENT_URL', 'https://pay.platbamobilom.sk/pay/');
        
        $this->line("   SMS_PAYMENT_PID: " . ($pid ? '✅ Nastavené (' . substr($pid, 0, 4) . '...)' : '❌ CHÝBA'));
        $this->line("   SMS_PAYMENT_SECRET: " . ($secret ? '✅ Nastavené (' . substr($secret, 0, 4) . '...)' : '❌ CHÝBA'));
        $this->line("   SMS_PAYMENT_URL: " . $url);
        
        // 2. Kontrola cache systému
        $this->info('💾 Cache systém:');
        $cacheDriver = config('cache.default');
        $testKey = 'sms_test_' . time();
        $testValue = 'test_data';
        
        cache()->put($testKey, $testValue, 60);
        $retrieved = cache()->get($testKey);
        cache()->forget($testKey);
        
        $this->line("   Cache driver: " . $cacheDriver);
        $this->line("   Cache test: " . ($retrieved === $testValue ? '✅ Funguje' : '❌ NEFUNGUJE'));
        
        // 3. Kontrola databázy
        $this->info('🗄️ Databáza:');
        try {
            $verificationCount = SmsVerification::count();
            $recentCount = SmsVerification::where('created_at', '>', now()->subHours(24))->count();
            $this->line("   Celkovo verifikácií: " . $verificationCount);
            $this->line("   Za posledných 24h: " . $recentCount);
            $this->line("   Databázové spojenie: ✅ Funguje");
        } catch (\Exception $e) {
            $this->line("   Databázové spojenie: ❌ CHYBA - " . $e->getMessage());
        }
        
        // 4. Test SMS Service
        $this->info('📱 SMS Service test:');
        try {
            $smsService = app(SmsPaymentService::class);
            $packages = $smsService->getSmsPackages();
            $this->line("   SMS balíčky: ✅ " . count($packages) . " dostupných");
            
            // Test offline spracovania
            $testResponse = $smsService->processSmsReceived('421903123456', 'ERO HELP', 'test_' . time());
            $this->line("   Offline spracovanie: " . ($testResponse ? '✅ Funguje' : '❌ NEFUNGUJE'));
        } catch (\Exception $e) {
            $this->line("   SMS Service: ❌ CHYBA - " . $e->getMessage());
        }
        
        // 5. Posledné SMS verifikácie
        $this->info('📊 Posledná aktivita:');
        $lastVerifications = SmsVerification::orderBy('created_at', 'desc')->limit(5)->get();
        
        if ($lastVerifications->count() > 0) {
            foreach ($lastVerifications as $verification) {
                $status = $verification->is_verified ? '✅ Overené' : ($verification->expires_at < now() ? '⏰ Expirované' : '⏳ Čaká');
                $this->line("   {$verification->created_at->format('d.m.Y H:i')} - {$verification->verification_code} ({$verification->package_code}) - {$status}");
            }
        } else {
            $this->line("   Žiadne SMS verifikácie v databáze");
        }
        
        // 6. Cache verifikácie
        $this->info('🔑 Aktívne cache verifikácie:');
        $cacheCount = 0;
        // Skúsime nájsť cache kľúče (toto je aproximácia)
        for ($i = 100000; $i <= 999999; $i++) {
            if (cache()->has('sms_verification_' . $i)) {
                $cacheCount++;
                if ($cacheCount <= 5) {
                    $data = cache()->get('sms_verification_' . $i);
                    $this->line("   Kód {$i}: {$data['package']['code']} - {$data['phone']}");
                }
            }
        }
        $this->line("   Celkovo v cache: ~{$cacheCount} verifikácií");
        
        $this->info('=====================================');
        if (!$pid || !$secret) {
            $this->error('⚠️  KRITICKÝ PROBLÉM: Chýbajú SMS_PAYMENT_PID alebo SMS_PAYMENT_SECRET!');
            $this->info('Riešenie: Skontrolujte .env súbor na produkčnom serveri.');
        } else {
            $this->info('✅ Základná konfigurácia vyzerá v poriadku.');
            $this->info('Ak SMS nefungujú, problém je pravdepodobne na strane PlatbaMobilom.sk');
        }
        
        return 0;
    }
} 