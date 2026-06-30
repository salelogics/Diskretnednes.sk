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
        // Oprav verification_photo cesty
        DB::table('ads')
            ->where('verification_photo', 'LIKE', 'storage/%')
            ->update([
                'verification_photo' => DB::raw("REPLACE(verification_photo, 'storage/', 'images/uploads/')")
            ]);

        // Oprav gallery_photos cesty
        $ads = DB::table('ads')
            ->whereNotNull('gallery_photos')
            ->where('gallery_photos', '!=', '[]')
            ->get();

        foreach ($ads as $ad) {
            $galleryPhotos = json_decode($ad->gallery_photos, true);
            
            if (is_array($galleryPhotos)) {
                $updated = false;
                
                foreach ($galleryPhotos as &$photo) {
                    if (is_string($photo) && str_starts_with($photo, 'storage/')) {
                        $photo = str_replace('storage/', 'images/uploads/', $photo);
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update(['gallery_photos' => json_encode($galleryPhotos)]);
                }
            }
        }
        
        echo "✅ Opravené cesty k fotkám zo storage/ na images/uploads/\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Vráť verification_photo cesty
        DB::table('ads')
            ->where('verification_photo', 'LIKE', 'images/uploads/%')
            ->update([
                'verification_photo' => DB::raw("REPLACE(verification_photo, 'images/uploads/', 'storage/')")
            ]);

        // Vráť gallery_photos cesty
        $ads = DB::table('ads')
            ->whereNotNull('gallery_photos')
            ->where('gallery_photos', '!=', '[]')
            ->get();

        foreach ($ads as $ad) {
            $galleryPhotos = json_decode($ad->gallery_photos, true);
            
            if (is_array($galleryPhotos)) {
                $updated = false;
                
                foreach ($galleryPhotos as &$photo) {
                    if (is_string($photo) && str_starts_with($photo, 'images/uploads/')) {
                        $photo = str_replace('images/uploads/', 'storage/', $photo);
                        $updated = true;
                    }
                }
                
                if ($updated) {
                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update(['gallery_photos' => json_encode($galleryPhotos)]);
                }
            }
        }
    }
};
