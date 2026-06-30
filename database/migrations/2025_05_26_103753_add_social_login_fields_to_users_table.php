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
        if (!Schema::hasColumn('users', 'google_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('google_id')->nullable();
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        if (!Schema::hasColumn('users', 'facebook_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('facebook_id')->nullable();
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        if (!Schema::hasColumn('users', 'apple_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('apple_id')->nullable();
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
            Schema::table('users', function (Blueprint $table) {
                $columnsToRemove = [];
                
                if (Schema::hasColumn('users', 'google_id')) {
                    $columnsToRemove[] = 'google_id';
                }
                
                if (Schema::hasColumn('users', 'facebook_id')) {
                    $columnsToRemove[] = 'facebook_id';
                }
                
                if (Schema::hasColumn('users', 'apple_id')) {
                    $columnsToRemove[] = 'apple_id';
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
