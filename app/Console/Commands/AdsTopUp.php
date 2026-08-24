<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\PaymentPackage;
use Illuminate\Console\Command;

class AdsTopUp extends Command
{
    protected $signature = 'ads:top-up
        {--target=50 : Cieľový počet viditeľných inzerátov}
        {--allow-missing-photo : Aktivovať aj inzeráty bez fotky (pozor: zobrazia sa ako prázdne dlaždice)}
        {--dry-run : Iba vypíše, čo by sa stalo, nič nezmení}';

    protected $description = 'Doplní počet verejne viditeľných inzerátov na cieľovú hodnotu aktiváciou draft/pending/inactive inzerátov.';

    public function handle(): int
    {
        $target = (int) $this->option('target');
        $dryRun = (bool) $this->option('dry-run');
        $requirePhoto = !$this->option('allow-missing-photo');

        $visible = Ad::active()->withActiveSubscription()->count();
        $needed = $target - $visible;

        $this->info("Viditeľných teraz: {$visible} / cieľ {$target}");

        if ($needed <= 0) {
            $this->info('Cieľ je už splnený, nič nerobím.');
            return self::SUCCESS;
        }

        $package = PaymentPackage::where('type', 'classic')->where('is_active', true)->first();

        if (!$package) {
            $this->error('Nenašiel sa aktívny balíček typu "classic" - bez neho sa aktivovať nedá.');
            return self::FAILURE;
        }

        // 'rejected' sa zámerne nikdy neaktivuje - to je konkrétne
        // moderátorské rozhodnutie, nie hromadná deaktivácia.
        $candidates = Ad::whereIn('status', ['draft', 'pending', 'inactive'])
            ->orderBy('created_at', 'asc')
            ->get();

        $this->line("Kandidátov (draft/pending/inactive): {$candidates->count()}");

        $activated = 0;
        $skippedNoPhoto = 0;

        foreach ($candidates as $ad) {
            if ($activated >= $needed) {
                break;
            }

            if ($requirePhoto && !$this->hasDisplayablePhoto($ad)) {
                $skippedNoPhoto++;
                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] aktivoval by som #{$ad->id} ({$ad->status}) - " . ($ad->nickname ?: 'bez prezývky'));
                $activated++;
                continue;
            }

            $this->activate($ad, $package);
            $activated++;
        }

        $this->line('');
        $this->info("Aktivovaných: {$activated}");

        if ($skippedNoPhoto > 0) {
            $this->warn("Preskočených pre chýbajúcu fotku: {$skippedNoPhoto}");
        }

        $shortfall = $needed - $activated;

        if ($shortfall > 0) {
            $this->warn("Stále chýba {$shortfall} do cieľa {$target} - v DB nie je dosť vhodných inzerátov.");
        } else {
            $this->info("Cieľ {$target} dosiahnutý.");
        }

        return self::SUCCESS;
    }

    /**
     * Robí to isté s dátami ako AdPayment::markAsCompleted(), ale zámerne
     * bez notifikácií: tá metóda posiela majiteľovi e-mail o "úspešnej
     * platbe" a adminom o "novej platbe". Pri hromadnom doplnení by to
     * znamenalo desiatky mätúcich e-mailov o platbách, ktoré nikto nespravil.
     */
    private function activate(Ad $ad, PaymentPackage $package): void
    {
        $expiresAt = $package->duration_days > 0 ? now()->addDays($package->duration_days) : null;

        AdPayment::create([
            'user_id' => $ad->user_id,
            'ad_id' => $ad->id,
            'payment_package_id' => $package->id,
            'payment_id' => str_pad((string) mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'amount' => 0,
            'currency' => 'EUR',
            'payment_method' => 'free',
            'status' => 'completed',
            'duration_days' => $package->duration_days,
            'is_featured' => $package->is_featured,
            'is_top_ad' => $package->is_top_ad,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => $expiresAt,
            'metadata' => [
                'package_name' => $package->name,
                'package_type' => $package->type,
                'bulk_activation' => true,
                'previous_status' => $ad->status,
            ],
        ]);

        $ad->update([
            'status' => 'active',
            'subscription_status' => 'active',
            'subscription_expires_at' => $expiresAt,
            'featured' => $package->is_featured,
            'top_ad' => $package->is_top_ad,
        ]);
    }

    /**
     * To isté, čo kontroluje partials/ad-grid.blade.php - vrátane overenia,
     * že súbor na disku existuje.
     */
    private function hasDisplayablePhoto(Ad $ad): bool
    {
        return (bool) $ad->verification_image_url || count($ad->gallery_image_urls) > 0;
    }
}
