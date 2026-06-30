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
        if (Schema::hasColumn('users', 'apple_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('apple_id');
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí odstrániť
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
};
