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
        // Skontroluj či existuje stĺpec gallery_photos predtým než ho upravíš
        if (Schema::hasColumn('ads', 'gallery_photos')) {
            // Oprava duplikátnych ciest v gallery_photos
            $ads = DB::table('ads')
                ->whereNotNull('gallery_photos')
                ->where('gallery_photos', '!=', '[]')
                ->get();

            foreach ($ads as $ad) {
                $galleryPhotos = json_decode($ad->gallery_photos, true);
                
                if (is_array($galleryPhotos)) {
                    $updated = false;
                    
                    foreach ($galleryPhotos as $index => $photo) {
                        if (is_string($photo) && str_contains($photo, 'ads/ads/gallery/')) {
                            $galleryPhotos[$index] = str_replace('ads/ads/gallery/', 'ads/gallery/', $photo);
                            $updated = true;
                        }
                    }
                    
                    if ($updated) {
                        DB::table('ads')
                            ->where('id', $ad->id)
                            ->update(['gallery_photos' => json_encode($galleryPhotos)]);
                        
                        echo "Updated gallery photos for ad ID: {$ad->id}\n";
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
        // Reverzia by bola komplikovaná, takže necháme prázdne
        // V prípade potreby by sa muselo obnoviť z backupu
    }
};
