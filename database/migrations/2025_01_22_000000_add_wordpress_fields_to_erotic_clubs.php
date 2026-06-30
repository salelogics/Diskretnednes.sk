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
        if (Schema::hasTable('erotic_clubs')) {
            Schema::table('erotic_clubs', function (Blueprint $table) {
                if (!Schema::hasColumn('erotic_clubs', 'wp_id')) {
                    $table->unsignedBigInteger('wp_id')->nullable()->after('id');
                    $table->index('wp_id');
                }
                
                if (!Schema::hasColumn('erotic_clubs', 'wp_modified_at')) {
                    $table->timestamp('wp_modified_at')->nullable()->after('updated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('erotic_clubs')) {
            Schema::table('erotic_clubs', function (Blueprint $table) {
                if (Schema::hasColumn('erotic_clubs', 'wp_id')) {
                    $table->dropIndex(['wp_id']);
                    $table->dropColumn('wp_id');
                }
                
                if (Schema::hasColumn('erotic_clubs', 'wp_modified_at')) {
                    $table->dropColumn('wp_modified_at');
                }
            });
        }
    }
}; 