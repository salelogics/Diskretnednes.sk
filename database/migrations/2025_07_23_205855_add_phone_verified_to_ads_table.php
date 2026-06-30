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
        // Pridaj phone_verified stĺpec ak neexistuje
        if (!Schema::hasColumn('ads', 'phone_verified')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->boolean('phone_verified')->default(false);
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        // Pridaj featured stĺpec ak neexistuje (nie is_featured)
        if (!Schema::hasColumn('ads', 'featured')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->boolean('featured')->default(false);
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        // Pridaj subscription_status stĺpec ak neexistuje
        if (!Schema::hasColumn('ads', 'subscription_status')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->enum('subscription_status', ['active', 'inactive', 'expired'])->default('inactive');
                });
            } catch (\Exception $e) {
                // Ignoruj ak sa stĺpec nepodarí pridať
            }
        }
        
        // Pridaj subscription_expires_at stĺpec ak neexistuje
        if (!Schema::hasColumn('ads', 'subscription_expires_at')) {
            try {
                Schema::table('ads', function (Blueprint $table) {
                    $table->timestamp('subscription_expires_at')->nullable();
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
        $columnsToRemove = ['phone_verified', 'featured', 'subscription_status', 'subscription_expires_at'];
        
        foreach ($columnsToRemove as $column) {
            if (Schema::hasColumn('ads', $column)) {
                try {
                    Schema::table('ads', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                } catch (\Exception $e) {
                    // Ignoruj ak sa stĺpec nepodarí odstrániť
                }
            }
        }
    }
};
