<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ad;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skontroluj či existuje stĺpec gallery_photos predtým než ho upravíš
        if (Schema::hasColumn('ads', 'gallery_photos')) {
            echo "Opravujem duplicitné cesty storage/ads/ads/gallery/ na storage/ads/gallery/gallery_\n";
            
            // Nájdi ads s duplicitným ads/ads/gallery/ v cestách
            $problematicAds = Ad::whereNotNull('gallery_photos')->get()->filter(function($ad) {
                if($ad->gallery_photos && is_array($ad->gallery_photos)) {
                    foreach($ad->gallery_photos as $photo) {
                        if(str_contains($photo, 'ads/ads/gallery/')) {
                            return true;
                        }
                    }
                }
                return false;
            });

            echo "Nájdených ads s duplicitným ads/ads/gallery/: " . $problematicAds->count() . "\n";

            foreach($problematicAds as $ad) {
                $galleryPhotos = $ad->gallery_photos;
                $updated = false;
                
                for($i = 0; $i < count($galleryPhotos); $i++) {
                    $photo = $galleryPhotos[$i];
                    
                    // Ak je cesta duplicitná storage/ads/ads/gallery/gallery_*.jpg
                    if(str_contains($photo, 'storage/ads/ads/gallery/')) {
                        // Extrahuj názov súboru
                        if(preg_match('/storage\/ads\/ads\/gallery\/(.+)$/', $photo, $matches)) {
                            $filename = $matches[1];
                            // Ak názov súboru nezačína s "gallery_", pridaj prefix
                            if(!str_starts_with($filename, 'gallery_')) {
                                $filename = 'gallery_' . $filename;
                            }
                            $galleryPhotos[$i] = 'storage/ads/gallery/' . $filename;
                            $updated = true;
                        }
                    }
                }
                
                if($updated) {
                    $ad->gallery_photos = $galleryPhotos;
                    $ad->save();
                    echo "Opravené cesty pre Ad ID: " . $ad->id . "\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skontroluj či existuje stĺpec gallery_photos predtým než ho upravíš
        if (Schema::hasColumn('ads', 'gallery_photos')) {
            // Vrátiť zmeny späť
            $ads = Ad::whereNotNull('gallery_photos')->get()->filter(function($ad) {
                if($ad->gallery_photos && is_array($ad->gallery_photos)) {
                    foreach($ad->gallery_photos as $photo) {
                        if(str_contains($photo, 'storage/ads/gallery/gallery_')) {
                            return true;
                        }
                    }
                }
                return false;
            });

            foreach($ads as $ad) {
                $galleryPhotos = $ad->gallery_photos;
                $updated = false;
                
                for($i = 0; $i < count($galleryPhotos); $i++) {
                    $photo = $galleryPhotos[$i];
                    
                    if(str_contains($photo, 'storage/ads/gallery/gallery_')) {
                        // Vrátiť späť na storage/ads/ads/gallery/
                        $filename = str_replace('storage/ads/gallery/gallery_', '', $photo);
                        $galleryPhotos[$i] = 'storage/ads/ads/gallery/' . $filename;
                        $updated = true;
                    }
                }
                
                if($updated) {
                    $ad->gallery_photos = $galleryPhotos;
                    $ad->save();
                }
            }
        }
    }
};
