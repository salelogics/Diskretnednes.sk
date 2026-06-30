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
        Schema::table('ads', function (Blueprint $table) {
            // WordPress tracking fields
            if (!Schema::hasColumn('ads', 'wp_id')) {
                $table->bigInteger('wp_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('ads', 'wp_modified_at')) {
                $table->timestamp('wp_modified_at')->nullable();
            }
            if (!Schema::hasColumn('ads', 'wp_slug')) {
                $table->string('wp_slug')->nullable();
            }
            if (!Schema::hasColumn('ads', 'wp_status')) {
                $table->string('wp_status')->nullable();
            }
            
            // WordPress meta fields that don't exist in Laravel model
            if (!Schema::hasColumn('ads', 'wp_email')) {
                $table->string('wp_email')->nullable();
            }
            if (!Schema::hasColumn('ads', 'wp_services_for')) {
                $table->json('wp_services_for')->nullable(); // i_ponukam_sluzby_pre
            }
            if (!Schema::hasColumn('ads', 'wp_weekly_hours')) {
                $table->json('wp_weekly_hours')->nullable(); // weekly schedule
            }
            if (!Schema::hasColumn('ads', 'wp_extra_services')) {
                $table->json('wp_extra_services')->nullable();
            }
            if (!Schema::hasColumn('ads', 'wp_subscription_type')) {
                $table->string('wp_subscription_type')->nullable(); // predplatne_1den
            }
            
            // WordPress media IDs for tracking
            if (!Schema::hasColumn('ads', 'wp_verification_photo_id')) {
                $table->bigInteger('wp_verification_photo_id')->nullable();
            }
            if (!Schema::hasColumn('ads', 'wp_gallery_photo_ids')) {
                $table->json('wp_gallery_photo_ids')->nullable();
            }
            if (!Schema::hasColumn('ads', 'wp_video_ids')) {
                $table->json('wp_video_ids')->nullable();
            }
        });

        // Add index only if wp_id column exists and index doesn't exist
        if (Schema::hasColumn('ads', 'wp_id')) {
            Schema::table('ads', function (Blueprint $table) {
                // Check if index doesn't already exist
                try {
                    $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('ads');
                    if (!isset($indexes['ads_wp_id_index'])) {
                        $table->index('wp_id');
                    }
                } catch (\Exception $e) {
                    // If we can't check indexes, try to create it anyway
                    try {
                        $table->index('wp_id');
                    } catch (\Exception $indexException) {
                        // Index might already exist, ignore
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // Drop index if it exists
            try {
                $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('ads');
                if (isset($indexes['ads_wp_id_index'])) {
                    $table->dropIndex(['wp_id']);
                }
            } catch (\Exception $e) {
                // Ignore if we can't check or drop index
            }
            
            // Drop columns only if they exist
            $columnsToCheck = [
                'wp_id',
                'wp_modified_at',
                'wp_slug',
                'wp_status',
                'wp_email',
                'wp_services_for',
                'wp_weekly_hours',
                'wp_extra_services',
                'wp_subscription_type',
                'wp_verification_photo_id',
                'wp_gallery_photo_ids',
                'wp_video_ids'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('ads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
