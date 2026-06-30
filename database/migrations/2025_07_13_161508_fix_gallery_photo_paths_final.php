<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ad;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skontroluj či existuje stĺpec gallery_photos predtým než ho upravíš
        if (Schema::hasColumn('ads', 'gallery_photos')) {
            // Nájdi ads s problematickými gallery cestami
            $problematicAds = Ad::whereNotNull('gallery_photos')->get()->filter(function($ad) {
                if($ad->gallery_photos && is_array($ad->gallery_photos)) {
                    foreach($ad->gallery_photos as $photo) {
                        if(preg_match('/^storage\/ads\/gallery_\d+\.jpg$/', $photo)) {
                            return true;
                        }
                    }
                }
                return false;
            });

            foreach($problematicAds as $ad) {
                $galleryPhotos = $ad->gallery_photos;
                $updated = false;
                
                for($i = 0; $i < count($galleryPhotos); $i++) {
                    $photo = $galleryPhotos[$i];
                    
                    // Ak je cesta v formáte storage/ads/gallery_*.jpg, oprav ju
                    if(preg_match('/^storage\/ads\/(gallery_\d+\.jpg)$/', $photo, $matches)) {
                        $filename = $matches[1];
                        $galleryPhotos[$i] = 'storage/ads/gallery/' . $filename;
                        $updated = true;
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
            // Nájdi ads s opravenými gallery cestami a vráť ich späť
            $ads = Ad::whereNotNull('gallery_photos')->get()->filter(function($ad) {
                if($ad->gallery_photos && is_array($ad->gallery_photos)) {
                    foreach($ad->gallery_photos as $photo) {
                        if(preg_match('/^storage\/ads\/gallery\/gallery_\d+\.jpg$/', $photo)) {
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
                    
                    // Ak je cesta v formáte storage/ads/gallery/gallery_*.jpg, vráť ju späť
                    if(preg_match('/^storage\/ads\/gallery\/(gallery_\d+\.jpg)$/', $photo, $matches)) {
                        $filename = $matches[1];
                        $galleryPhotos[$i] = 'storage/ads/' . $filename;
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
