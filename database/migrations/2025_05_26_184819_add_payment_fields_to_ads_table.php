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
        
        Schema::table('ads', function (Blueprint $table) {
            // Pridám iba stĺpce ktoré ešte neexistujú
            if (!Schema::hasColumn('ads', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            
            // Preskočím is_top_ad, pretože už existuje ako top_ad z predchádzajúcej migrácie
            
            if (!Schema::hasColumn('ads', 'featured_until')) {
                $table->timestamp('featured_until')->nullable();
            }
            
            if (!Schema::hasColumn('ads', 'top_ad_until')) {
                $table->timestamp('top_ad_until')->nullable();
            }
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
            // Odstraním iba stĺpce ktoré existujú
            $columnsToRemove = [];
            
            if (Schema::hasColumn('ads', 'is_featured')) {
                $columnsToRemove[] = 'is_featured';
            }
            
            if (Schema::hasColumn('ads', 'featured_until')) {
                $columnsToRemove[] = 'featured_until';
            }
            
            if (Schema::hasColumn('ads', 'top_ad_until')) {
                $columnsToRemove[] = 'top_ad_until';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
