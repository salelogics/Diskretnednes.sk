<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skontroluj či existuje stĺpec verification_photo predtým než ho upravíš
        if (Schema::hasColumn('ads', 'verification_photo')) {
            // Oprav verification_photo cesty v ads tabuľke
            DB::statement("
                UPDATE ads 
                SET verification_photo = CASE 
                    WHEN verification_photo LIKE 'storage/ads/verification_%' 
                        AND verification_photo NOT LIKE 'storage/ads/verification/%' 
                        THEN verification_photo
                    WHEN verification_photo LIKE 'verification_%' 
                        AND verification_photo NOT LIKE 'storage/%' 
                        THEN CONCAT('storage/ads/', verification_photo)
                    ELSE verification_photo
                END
                WHERE verification_photo IS NOT NULL 
                AND verification_photo NOT LIKE 'http%'
            ");
        }
        
        // Skontroluj či existuje stĺpec gallery_photos predtým než ho upravíš
        if (Schema::hasColumn('ads', 'gallery_photos')) {
            // Aktualizuj gallery_photos JSON pole
            $ads = DB::table('ads')
                ->whereNotNull('gallery_photos')
                ->where('gallery_photos', '!=', '[]')
                ->get();
                
            foreach ($ads as $ad) {
                $gallery = json_decode($ad->gallery_photos, true);
                if (is_array($gallery)) {
                    $updated = false;
                    foreach ($gallery as $key => $photo) {
                        if (is_string($photo) && !str_starts_with($photo, 'http') && !str_starts_with($photo, 'storage/')) {
                            $gallery[$key] = 'storage/ads/' . $photo;
                            $updated = true;
                        }
                    }
                    
                    if ($updated) {
                        DB::table('ads')
                            ->where('id', $ad->id)
                            ->update(['gallery_photos' => json_encode($gallery)]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback nie je potrebný pre túto migráciu
        // pretože opravujeme len cesty k súborom
    }
};
