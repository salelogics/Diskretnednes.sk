<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsVerification;
use App\Models\Ad;
use App\Services\SmsPaymentService;
use Illuminate\Support\Facades\Log;

class ProcessPendingSmsVerifications extends Command
{
    protected $signature = 'sms:process-pending-verifications 
                            {--dry-run : Just show what would be processed}
                            {--limit=50 : Limit number of records to process}';

    protected $description = 'Process verified SMS verifications that were not used to activate ads';

    public function handle()
    {
        $this->info('🔍 Hľadám overené SMS verifikácie ktoré neaktivovali inzeráty...');
        
        // Nájdeme verified verifikácie, ktoré nemajú used_at alebo is_payment_confirmed = false
        $verifications = SmsVerification::where('status', 'verified')
            ->where('is_verified', true)
            ->where(function ($query) {
                $query->whereNull('used_at')
                      ->orWhere('is_payment_confirmed', false);
            })
            ->whereNotNull('ad_id')
            ->with(['ad'])
            ->limit($this->option('limit'))
            ->get();

        if ($verifications->isEmpty()) {
            $this->info('✅ Nenašli sa žiadne nevyužité verifikácie.');
            return 0;
        }

        $this->info("📱 Našiel sa {$verifications->count()} nevyužitých verifikácií:");

        $processed = 0;
        $errors = 0;
        $alreadyActive = 0;

        foreach ($verifications as $verification) {
            try {
                $ad = $verification->ad;
                
                if (!$ad) {
                    $this->warn("❌ Inzerát #{$verification->ad_id} neexistuje (verification #{$verification->id})");
                    $errors++;
                    continue;
                }

                // Skontrolujeme či je inzerát už aktívny
                $isActive = $ad->status === 'active' && $ad->subscription_status === 'active';
                
                $this->line("📋 Verification #{$verification->id}:");
                $this->line("   - Kód: {$verification->verification_code}");
                $this->line("   - Inzerát: #{$ad->id} ({$ad->title})");
                $this->line("   - Balíček: {$verification->package_type} na {$verification->package_duration} dní");
                $this->line("   - Cena: €{$verification->price}");
                $this->line("   - Overený: {$verification->verified_at?->format('d.m.Y H:i')}");
                $this->line("   - Status inzerátu: {$ad->status} / {$ad->subscription_status}");
                
                if ($isActive) {
                    $this->line("   ✅ Inzerát je už aktívny - označím verifikáciu ako použitú");
                    
                    if (!$this->option('dry-run')) {
                        $verification->update([
                            'used_at' => now(),
                            'is_payment_confirmed' => true
                        ]);
                    }
                    
                    $alreadyActive++;
                } else {
                    $this->line("   🔄 Pokúsim sa aktivovať inzerát...");
                    
                    if (!$this->option('dry-run')) {
                        $smsService = app(SmsPaymentService::class);
                        $method = new \ReflectionMethod($smsService, 'activateAdViaPayment');
                        $method->setAccessible(true);
                        $payment = $method->invoke($smsService, $verification);
                        
                        $this->line("   ✅ Inzerát aktivovaný! Payment ID: {$payment->payment_id}");
                    } else {
                        $this->line("   🔥 Inzerát by bol aktivovaný (dry-run)");
                    }
                    
                    $processed++;
                }

                $this->line("");

            } catch (\Exception $e) {
                $this->error("❌ Chyba pri spracovaní verification #{$verification->id}: " . $e->getMessage());
                Log::error('SMS verification processing error', [
                    'verification_id' => $verification->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errors++;
            }
        }

        // Súhrn
        $this->line("📊 SÚHRN:");
        $this->info("✅ Spracované: {$processed}");
        $this->info("ℹ️  Už aktívne: {$alreadyActive}");
        
        if ($errors > 0) {
            $this->error("❌ Chyby: {$errors}"); 
        }

        if ($this->option('dry-run')) {
            $this->warn("🔍 Toto bol iba test (--dry-run). Na skutočné spracovanie spusti bez --dry-run");
        }

        return 0;
    }
}