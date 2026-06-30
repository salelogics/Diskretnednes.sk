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
        // Kontrola či tabuľka existuje pred úpravou
        if (!Schema::hasTable('blog_posts')) {
            // Tabuľka ešte neexistuje, preskočiť migráciu
            return;
        }
        
        Schema::table('blog_posts', function (Blueprint $table) {
            // Kontrola či stĺpce už neexistujú
            if (!Schema::hasColumn('blog_posts', 'wp_id')) {
                $table->unsignedBigInteger('wp_id')->nullable()->after('id');
                $table->index('wp_id');
            }
            
            if (!Schema::hasColumn('blog_posts', 'wp_author_id')) {
                $table->unsignedBigInteger('wp_author_id')->nullable()->after('author_id');
                $table->index('wp_author_id');
            }
            
            if (!Schema::hasColumn('blog_posts', 'wp_featured_media_id')) {
                $table->unsignedBigInteger('wp_featured_media_id')->nullable()->after('image_path');
            }
            
            if (!Schema::hasColumn('blog_posts', 'wp_modified_at')) {
                $table->timestamp('wp_modified_at')->nullable()->after('updated_at');
            }
            
            if (!Schema::hasColumn('blog_posts', 'wp_categories')) {
                $table->json('wp_categories')->nullable()->after('wp_modified_at');
            }
            
            if (!Schema::hasColumn('blog_posts', 'wp_tags')) {
                $table->json('wp_tags')->nullable()->after('wp_categories');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('blog_posts')) {
            return;
        }
        
        Schema::table('blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('blog_posts', 'wp_id')) {
                $table->dropIndex(['wp_id']);
                $table->dropColumn('wp_id');
            }
            
            if (Schema::hasColumn('blog_posts', 'wp_author_id')) {
                $table->dropIndex(['wp_author_id']);
                $table->dropColumn('wp_author_id');
            }
            
            if (Schema::hasColumn('blog_posts', 'wp_featured_media_id')) {
                $table->dropColumn('wp_featured_media_id');
            }
            
            if (Schema::hasColumn('blog_posts', 'wp_modified_at')) {
                $table->dropColumn('wp_modified_at');
            }
            
            if (Schema::hasColumn('blog_posts', 'wp_categories')) {
                $table->dropColumn('wp_categories');
            }
            
            if (Schema::hasColumn('blog_posts', 'wp_tags')) {
                $table->dropColumn('wp_tags');
            }
        });
    }
}; 