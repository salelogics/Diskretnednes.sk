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
        if (!Schema::hasColumn('ads', 'nickname')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->string('nickname');
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
        if (Schema::hasColumn('ads', 'nickname')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->dropColumn('nickname');
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí odstrániť
            }
        }
    }
};
