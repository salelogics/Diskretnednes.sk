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
        try {
            Schema::table('ads', function (Blueprint $table) {
                // Kompozitný index pre hlavný query na home stránke (ak existujú všetky stĺpce)
                if (Schema::hasColumn('ads', 'status') && 
                    Schema::hasColumn('ads', 'subscription_status') && 
                    Schema::hasColumn('ads', 'subscription_expires_at') && 
                    Schema::hasColumn('ads', 'created_at')) {
                    try {
                        $table->index(['status', 'subscription_status', 'subscription_expires_at', 'created_at'], 'ads_home_query_index');
                    } catch (\Exception $e) {
                        // Index už existuje alebo iná chyba, ignoruj
                    }
                }
                
                // Index pre filtrovanie podľa offer_type
                if (Schema::hasColumn('ads', 'offer_type') && 
                    Schema::hasColumn('ads', 'status') && 
                    Schema::hasColumn('ads', 'subscription_status')) {
                    try {
                        $table->index(['offer_type', 'status', 'subscription_status'], 'ads_offer_type_index');
                    } catch (\Exception $e) {
                        // Index už existuje alebo iná chyba, ignoruj
                    }
                }
                
                // Index pre filtrovanie podľa veku
                if (Schema::hasColumn('ads', 'age') && 
                    Schema::hasColumn('ads', 'status') && 
                    Schema::hasColumn('ads', 'subscription_status')) {
                    try {
                        $table->index(['age', 'status', 'subscription_status'], 'ads_age_index');
                    } catch (\Exception $e) {
                        // Index už existuje alebo iná chyba, ignoruj
                    }
                }
                
                // Index pre top_ad (ak existuje stĺpec)
                if (Schema::hasColumn('ads', 'top_ad') && 
                    Schema::hasColumn('ads', 'status') && 
                    Schema::hasColumn('ads', 'subscription_status')) {
                    try {
                        $table->index(['top_ad', 'status', 'subscription_status'], 'ads_top_ad_index');
                    } catch (\Exception $e) {
                        // Index už existuje alebo iná chyba, ignoruj
                    }
                }
                
                // Index pre current_availability (ak existuje stĺpec)
                if (Schema::hasColumn('ads', 'current_availability')) {
                    try {
                        $table->index(['current_availability'], 'ads_availability_index');
                    } catch (\Exception $e) {
                        // Index už existuje alebo iná chyba, ignoruj
                    }
                }
            });
        } catch (\Exception $e) {
            // V prípade globálnej chyby, ignoruj
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('ads', function (Blueprint $table) {
                try {
                    $table->dropIndex('ads_home_query_index');
                } catch (\Exception $e) {
                    // Index neexistuje, ignoruj
                }
                
                try {
                    $table->dropIndex('ads_offer_type_index');
                } catch (\Exception $e) {
                    // Index neexistuje, ignoruj
                }
                
                try {
                    $table->dropIndex('ads_age_index');
                } catch (\Exception $e) {
                    // Index neexistuje, ignoruj
                }
                
                if (Schema::hasColumn('ads', 'top_ad')) {
                    try {
                        $table->dropIndex('ads_top_ad_index');
                    } catch (\Exception $e) {
                        // Index neexistuje, ignoruj
                    }
                }
                
                if (Schema::hasColumn('ads', 'current_availability')) {
                    try {
                        $table->dropIndex('ads_availability_index');
                    } catch (\Exception $e) {
                        // Index neexistuje, ignoruj
                    }
                }
            });
        } catch (\Exception $e) {
            // V prípade globálnej chyby pri rollback, ignoruj
        }
    }
};
