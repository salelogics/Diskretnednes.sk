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
        if (!Schema::hasColumn('ads', 'verification_code')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->string('verification_code', 6)->nullable();
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        if (!Schema::hasColumn('ads', 'verification_expires_at')) {
            try {
                Schema::table('ads', function (Blueprint $table) {  
                    $table->timestamp('verification_expires_at')->nullable();
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
        try {
            Schema::table('ads', function (Blueprint $table) {
                $columnsToRemove = [];
                
                if (Schema::hasColumn('ads', 'verification_code')) {
                    $columnsToRemove[] = 'verification_code';
                }
                
                if (Schema::hasColumn('ads', 'verification_expires_at')) {
                    $columnsToRemove[] = 'verification_expires_at';
                }
                
                if (!empty($columnsToRemove)) {
                    $table->dropColumn($columnsToRemove);
                }
            });
        } catch (\Exception $e) {
            // Ignoruj ak sa stĺpce nepodarí odstrániť
        }
    }
};
