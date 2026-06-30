<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;

class UpdateImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:update-image-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualizuje cesty k obrázkom z storage/ads na ads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== AKTUALIZÁCIA CIEST K OBRÁZKOM ===');
        
        $ads = Ad::whereNotNull('verification_photo')
                 ->orWhereNotNull('gallery_photos')
                 ->get();
        
        $this->info("Našiel som {$ads->count()} inzerátov s obrázkami");
        
        $updatedCount = 0;
        
        foreach ($ads as $ad) {
            $updated = false;
            
            // Aktualizuj verifikačnú fotku
            if ($ad->verification_photo && str_starts_with($ad->verification_photo, 'storage/ads/')) {
                $ad->verification_photo = str_replace('storage/ads/', 'ads/', $ad->verification_photo);
                $updated = true;
                $this->line("  ✅ Aktualizovaná verifikačná fotka pre inzerát ID: {$ad->id}");
            }
            
            // Aktualizuj galériu
            if ($ad->gallery_photos && is_array($ad->gallery_photos)) {
                $updatedGallery = [];
                $galleryUpdated = false;
                
                foreach ($ad->gallery_photos as $photo) {
                    if (is_string($photo) && str_starts_with($photo, 'storage/ads/')) {
                        $updatedGallery[] = str_replace('storage/ads/', 'ads/', $photo);
                        $galleryUpdated = true;
                    } else {
                        $updatedGallery[] = $photo;
                    }
                }
                
                if ($galleryUpdated) {
                    $ad->gallery_photos = $updatedGallery;
                    $updated = true;
                    $this->line("  ✅ Aktualizovaná galéria pre inzerát ID: {$ad->id}");
                }
            }
            
            if ($updated) {
                $ad->save();
                $updatedCount++;
            }
        }
        
        $this->info('');
        $this->info("=== HOTOVO ===");
        $this->info("Aktualizovaných inzerátov: {$updatedCount}");
        
        // Ukážka aktualizovaných ciest
        $this->info('');
        $this->info('=== UKÁŽKA AKTUALIZOVANÝCH CIEST ===');
        
        $sampleAds = Ad::whereNotNull('verification_photo')->limit(3)->get();
        foreach ($sampleAds as $ad) {
            $this->line("Inzerát ID {$ad->id}:");
            $this->line("  Verifikačná: {$ad->verification_photo}");
            if ($ad->gallery_photos && count($ad->gallery_photos) > 0) {
                $this->line("  Galéria: " . implode(', ', array_slice($ad->gallery_photos, 0, 2)));
            }
        }
        
        return 0;
    }
}
