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
        // Kontrola či tabuľka existuje
        if (!Schema::hasTable('ad_payments')) {
            return;
        }
        
        Schema::table('ad_payments', function (Blueprint $table) {
            // Stripe specific fields - pridám iba tie ktoré neexistujú
            if (!Schema::hasColumn('ad_payments', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable();
            }
            
            if (!Schema::hasColumn('ad_payments', 'stripe_checkout_session_id')) {
                $table->string('stripe_checkout_session_id')->nullable();
            }
            
            if (!Schema::hasColumn('ad_payments', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable();
            }
            
            if (!Schema::hasColumn('ad_payments', 'stripe_metadata')) {
                $table->json('stripe_metadata')->nullable();
            }
        });
        
        // Pridám indexy samostatne, s kontrolou
        Schema::table('ad_payments', function (Blueprint $table) {
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM ad_payments WHERE Key_name = 'ad_payments_stripe_payment_intent_id_index'");
            if (empty($indexes)) {
                $table->index('stripe_payment_intent_id');
            }
            
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM ad_payments WHERE Key_name = 'ad_payments_stripe_checkout_session_id_index'");
            if (empty($indexes)) {
                $table->index('stripe_checkout_session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ad_payments')) {
            return;
        }
        
        Schema::table('ad_payments', function (Blueprint $table) {
            // Odstraním indexy ak existujú
            try {
                $table->dropIndex(['stripe_payment_intent_id']);
            } catch (\Exception $e) {
                // Index neexistuje, ignoruj
            }
            
            try {
                $table->dropIndex(['stripe_checkout_session_id']);
            } catch (\Exception $e) {
                // Index neexistuje, ignoruj
            }
            
            // Odstraním stĺpce ak existujú
            $columnsToRemove = [];
            
            if (Schema::hasColumn('ad_payments', 'stripe_payment_intent_id')) {
                $columnsToRemove[] = 'stripe_payment_intent_id';
            }
            
            if (Schema::hasColumn('ad_payments', 'stripe_checkout_session_id')) {
                $columnsToRemove[] = 'stripe_checkout_session_id';
            }
            
            if (Schema::hasColumn('ad_payments', 'stripe_customer_id')) {
                $columnsToRemove[] = 'stripe_customer_id';
            }
            
            if (Schema::hasColumn('ad_payments', 'stripe_metadata')) {
                $columnsToRemove[] = 'stripe_metadata';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
