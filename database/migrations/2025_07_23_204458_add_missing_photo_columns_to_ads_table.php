<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pridaj verification_photo stĺpec ak neexistuje
        if (!Schema::hasColumn('ads', 'verification_photo')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->string('verification_photo')->nullable();
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        // Pridaj gallery_photos stĺpec ak neexistuje  
        if (!Schema::hasColumn('ads', 'gallery_photos')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->json('gallery_photos')->nullable();
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Odstráň stĺpce ak existujú
        if (Schema::hasColumn('ads', 'verification_photo')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->dropColumn('verification_photo');
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí odstrániť
            }
        }
        
        if (Schema::hasColumn('ads', 'gallery_photos')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->dropColumn('gallery_photos');
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí odstrániť
            }
        }
    }
};
