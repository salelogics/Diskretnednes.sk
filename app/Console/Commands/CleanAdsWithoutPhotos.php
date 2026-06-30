<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;

class CleanAdsWithoutPhotos extends Command
{
    protected $signature = 'ads:clean-without-photos {--dry-run : Len ukáž čo by sa vymazalo}';
    protected $description = 'Vymaže inzeráty, ktoré nemajú žiadne fotky';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Hľadám inzeráty bez fotiek...');
        
        if ($dryRun) {
            $this->warn('DRY RUN - žiadne inzeráty sa nevymažú');
        }

        // Nájdi inzeráty bez fotiek
        $ads = Ad::whereNull('verification_photo')
            ->whereNull('gallery_photos')
            ->orWhere('gallery_photos', '[]')
            ->orWhere('gallery_photos', '""')
            ->get();

        $this->info("Našiel som " . $ads->count() . " inzerátov bez fotiek:");

        foreach ($ads as $ad) {
            $this->info("- {$ad->nickname} (ID: {$ad->id}, WP ID: {$ad->wp_id})");
            
            if (!$dryRun) {
                try {
                    $ad->delete();
                    $this->info("  ✅ Vymazaný");
                } catch (\Exception $e) {
                    $this->error("  ❌ Chyba pri mazaní: " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info("=== VÝSLEDKY ===");
        $this->info("Celkovo nájdených: " . $ads->count());
        if (!$dryRun) {
            $this->info("Všetky inzeráty bez fotiek boli vymazané.");
        }
    }
} 