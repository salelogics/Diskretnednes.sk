<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsVerification;

class TestSmsCreate extends Command
{
    protected $signature = 'test:sms-create {code=654321}';
    protected $description = 'Vytvorí test SMS verifikáciu s kódom';

    public function handle()
    {
        $code = $this->argument('code');
        
        // Vytvorím test verifikáciu v databáze
        $verification = SmsVerification::create([
            'verification_code' => $code,
            'msisdn' => '0903123456',  
            'sms_id' => 'TEST_' . time(),
            'sms_code' => 'ERO CC3',  // Pridám povinné pole
            'package_code' => 'CC3',
            'package_type' => 'premium',
            'package_duration' => 7,
            'price' => 7.00,
            'status' => 'pending',
            'is_verified' => false,
            'is_payment_confirmed' => false,
            'expires_at' => now()->addMinutes(30)
        ]);
        
        // Aj do cache
        $cacheKey = 'sms_verification_' . $code;
        $cacheData = [
            'phone' => '0903123456',
            'package' => [
                'code' => 'CC3',
                'type' => 'premium',
                'duration' => 7,
                'price' => 7.00,
                'description' => 'Premium 7 dní'
            ],
            'created_at' => now(),
            'expires_at' => now()->addMinutes(30)
        ];
        
        cache()->put($cacheKey, $cacheData, 1800); // 30 minút
        
        $this->info("✅ Test SMS verifikácia vytvorená!");
        $this->line("📱 Kód: {$code}");
        $this->line("🗄️ DB ID: {$verification->id}");
        $this->line("📋 Cache kľúč: {$cacheKey}");
        $this->line("⏰ Expiruje za 30 minút");
        $this->line("");
        $this->line("🧪 Teraz môžete testovať SMS kód {$code} v aplikácii!");
        
        return 0;
    }
}
