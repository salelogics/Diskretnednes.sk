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
            // Skontroluj a uprav dátumové polia na nullable
            if (Schema::hasColumn('invoices', 'issue_date')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->date('issue_date')->nullable()->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'due_date')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->date('due_date')->nullable()->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'delivery_date')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->date('delivery_date')->nullable()->change();
                });
            }
            
            // Skontroluj a uprav finančné polia na nullable s default 0
            if (Schema::hasColumn('invoices', 'subtotal')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('subtotal', 10, 2)->nullable()->default(0)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'tax_amount')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('tax_amount', 10, 2)->nullable()->default(0)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'total_amount')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('total_amount', 10, 2)->nullable()->default(0)->change();
                });
            }
            
            // Skontroluj a uprav JSON polia na nullable
            if (Schema::hasColumn('invoices', 'supplier_data')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->json('supplier_data')->nullable()->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'payment_info')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->json('payment_info')->nullable()->change();
                });
            }
            
        } catch (\Exception $e) {
            // V prípade chyby, ignoruj a pokračuj
            // Log::warning('Invoice migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // Vrátime späť na povinné polia iba ak existujú
            if (Schema::hasColumn('invoices', 'issue_date')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->date('issue_date')->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'due_date')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->date('due_date')->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'delivery_date')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->date('delivery_date')->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'subtotal')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('subtotal', 10, 2)->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'tax_amount')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('tax_amount', 10, 2)->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'total_amount')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('total_amount', 10, 2)->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'supplier_data')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->json('supplier_data')->nullable(false)->change();
                });
            }
            
            if (Schema::hasColumn('invoices', 'payment_info')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->json('payment_info')->nullable(false)->change();
                });
            }
            
        } catch (\Exception $e) {
            // V prípade chyby pri rollback, ignoruj
        }
    }
}; 