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
        // Pridaj is_active stĺpec ak neexistuje
        if (!Schema::hasColumn('erotic_clubs', 'is_active')) {
            try {
                Schema::table('erotic_clubs', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true);
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        // Pridaj deleted_at stĺpec pre soft deletes ak neexistuje
        if (!Schema::hasColumn('erotic_clubs', 'deleted_at')) {
            try {
                Schema::table('erotic_clubs', function (Blueprint $table) {
                    $table->timestamp('deleted_at')->nullable();
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        // Pridaj position stĺpec pre usporiadanie ak neexistuje
        if (!Schema::hasColumn('erotic_clubs', 'position')) {
            try {
                Schema::table('erotic_clubs', function (Blueprint $table) {
                    $table->integer('position')->default(0);
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
        $columnsToRemove = ['is_active', 'deleted_at', 'position'];
        
        foreach ($columnsToRemove as $column) {
            if (Schema::hasColumn('erotic_clubs', $column)) {
                try {
                    Schema::table('erotic_clubs', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                } catch (\Exception $e) {
                    // Ignoruj ak sa stĺpec nepodarí odstrániť
                }
            }
        }
    }
};
