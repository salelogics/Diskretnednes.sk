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
        // Kontrola či tabuľka existuje
        if (!Schema::hasTable('ads')) {
            return;
        }
        
        // Kontrola či stĺpec už neexistuje
        if (Schema::hasColumn('ads', 'top_ad')) {
            return;
        }
        
        Schema::table('ads', function (Blueprint $table) {
            // Odstránené after('featured') - jednoducho pridám na koniec
            $table->boolean('top_ad')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ads')) {
            return;
        }
        
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'top_ad')) {
                $table->dropColumn('top_ad');
            }
        });
    }
};
