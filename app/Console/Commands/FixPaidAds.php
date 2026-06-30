<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use App\Models\AdPayment;

class FixPaidAds extends Command
{
    protected $signature = 'ads:fix-paid-ads';
    protected $description = 'Opraví inzeráty s aktivným predplatným ale neaktívnym statusom';

    public function handle()
    {
        $this->info('Hľadám inzeráty s aktivným predplatným ale neaktívnym statusom...');

        // Nájdi inzeráty s aktivným predplatným ale statusom != 'active'
        $ads = Ad::where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->where('status', '!=', 'active')
            ->get();

        $this->info("Našiel som {$ads->count()} inzerátov na opravu.");

        $fixed = 0;
        foreach ($ads as $ad) {
            // Skontroluj či má skutočne zaplatené predplatné
            $hasCompletedPayment = AdPayment::where('ad_id', $ad->id)
                ->where('status', 'completed')
                ->where('subscription_ends_at', '>', now())
                ->exists();

            if ($hasCompletedPayment) {
                $ad->update(['status' => 'active']);
                $fixed++;
                $this->info("Opravil som inzerát #{$ad->id} ({$ad->nickname})");
            }
        }

        $this->info("Opravil som {$fixed} inzerátov.");
        return Command::SUCCESS;
    }
} 