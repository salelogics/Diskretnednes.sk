<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;

class AdsPhotoAudit extends Command
{
    protected $signature = 'ads:photo-audit {--target=50 : Cieľový počet viditeľných inzerátov}';

    protected $description = 'Spočíta, koľko inzerátov má použiteľnú fotku a koľko sa ich ešte dá aktivovať na hlavnú stránku.';

    public function handle(): int
    {
        $target = (int) $this->option('target');

        $visible = Ad::active()->withActiveSubscription()->count();

        $this->info("Aktuálne viditeľných na webe: {$visible} (cieľ: {$target})");
        $this->line('');

        $rows = [];
        $availableWithPhoto = 0;

        foreach (['draft', 'pending', 'inactive', 'rejected', 'active'] as $status) {
            $ads = Ad::where('status', $status)->get();

            $withColumn = $ads->filter(fn (Ad $ad) => $this->hasPhotoValue($ad))->count();
            $withUsable = $ads->filter(fn (Ad $ad) => $this->hasDisplayablePhoto($ad))->count();

            // Kandidáti na doplnenie: to isté, čo berie migrácia (rejected sa
            // zámerne nikdy nepoužíva - to je moderátorské rozhodnutie).
            if (in_array($status, ['draft', 'pending', 'inactive'], true)) {
                $availableWithPhoto += $withUsable;
            }

            $rows[] = [
                $status,
                $ads->count(),
                $withColumn,
                $withUsable,
                $withColumn - $withUsable,
            ];
        }

        $this->table(
            ['Status', 'Spolu', 'Má cestu k fotke', 'Fotka reálne existuje', 'Cesta bez súboru'],
            $rows
        );

        $this->line('');
        $this->info("Dostupných na aktiváciu (draft+pending+inactive, s reálnou fotkou): {$availableWithPhoto}");

        $missing = $target - $visible;

        if ($missing <= 0) {
            $this->info("Cieľ {$target} je už splnený.");
            return self::SUCCESS;
        }

        if ($availableWithPhoto >= $missing) {
            $this->info("Chýba {$missing} - v DB je dosť kandidátov, cieľ sa dá splniť.");
        } else {
            $shortfall = $missing - $availableWithPhoto;
            $this->warn("Chýba {$missing}, ale kandidátov s fotkou je len {$availableWithPhoto} - stále bude chýbať {$shortfall}.");
            $this->warn('Bez nových inzerátov (alebo doplnenia fotiek k existujúcim) sa na ' . $target . ' nedostaneme.');
        }

        return self::SUCCESS;
    }

    /**
     * Iba stĺpce v DB - "tvári sa, že fotku má".
     */
    private function hasPhotoValue(Ad $ad): bool
    {
        if (!empty($ad->verification_photo)) {
            return true;
        }

        return is_array($ad->gallery_photos) && count($ad->gallery_photos) > 0;
    }

    /**
     * To isté, čo kontroluje partials/ad-grid.blade.php aj aktivačné
     * migrácie - vrátane overenia, že súbor na disku naozaj existuje.
     */
    private function hasDisplayablePhoto(Ad $ad): bool
    {
        return (bool) $ad->verification_image_url || count($ad->gallery_image_urls) > 0;
    }
}
