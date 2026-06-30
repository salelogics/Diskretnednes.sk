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
            // Pridáme všetky chýbajúce stĺpce ktoré sa používajú v seederoch
            
            if (!Schema::hasColumn('ads', 'clicks')) {
                $table->integer('clicks')->default(0)->after('views');
            }
            
            if (!Schema::hasColumn('ads', 'subscription_status')) {
                $table->enum('subscription_status', ['active', 'inactive', 'expired'])->default('inactive')->after('status');
            }
            
            if (!Schema::hasColumn('ads', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
            }
            
            if (!Schema::hasColumn('ads', 'featured')) {
                $table->boolean('featured')->default(false)->after('subscription_expires_at');
            }
            
            if (!Schema::hasColumn('ads', 'top_ad')) {
                $table->boolean('top_ad')->default(false)->after('featured');
            }
            
            if (!Schema::hasColumn('ads', 'phone_verified')) {
                $table->boolean('phone_verified')->default(false)->after('top_ad');
            }
            
            if (!Schema::hasColumn('ads', 'nickname')) {
                $table->string('nickname')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('ads', 'experience')) {
                $table->string('experience')->nullable()->after('girl_selection');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $columnsToRemove = [
                'clicks', 'subscription_status', 'subscription_expires_at', 
                'featured', 'top_ad', 'phone_verified', 'nickname', 'experience'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('ads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
