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
            // Oprava ciest pre verification fotky na hostingu
            $ads = DB::table('ads')
                ->whereNotNull('verification_photo')
                ->where('verification_photo', '!=', '')
                ->get();

            foreach ($ads as $ad) {
                $verificationPhoto = $ad->verification_photo;
                $updated = false;
                
                // Oprava ciest typu storage/ads/verification_XXXXX.jpg na storage/ads/verification/verification_XXXXX.jpg
                if (preg_match('/^storage\/ads\/verification_(\d+)\.jpg$/', $verificationPhoto, $matches)) {
                    $newPath = "storage/ads/verification/verification_{$matches[1]}.jpg";
                    $verificationPhoto = $newPath;
                    $updated = true;
                }
                
                if ($updated) {
                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update(['verification_photo' => $verificationPhoto]);
                    
                    echo "Updated verification photo for ad ID: {$ad->id} -> {$verificationPhoto}\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skontroluj či existuje stĺpec verification_photo predtým než ho upravíš
        if (Schema::hasColumn('ads', 'verification_photo')) {
            // Reverzia - vráť cesty späť na pôvodnú formu
            $ads = DB::table('ads')
                ->whereNotNull('verification_photo')
                ->where('verification_photo', 'LIKE', 'storage/ads/verification/verification_%')
                ->get();

            foreach ($ads as $ad) {
                if (preg_match('/^storage\/ads\/verification\/(verification_\d+\.jpg)$/', $ad->verification_photo, $matches)) {
                    $oldPath = "storage/ads/{$matches[1]}";
                    
                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update(['verification_photo' => $oldPath]);
                }
            }
        }
    }
};
