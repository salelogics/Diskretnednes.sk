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
        if (Schema::hasColumn('invoices', 'invoice_number')) {
            try {
                Schema::table('invoices', function (Blueprint $table) {
                    // Zmením invoice_number na nullable aby sa mohlo vytvoriť bez hodnoty
                    // a potom sa doplní automaticky
                    $table->string('invoice_number')->nullable()->change();
                });
            } catch (\Exception $e) {
                // V prípade chyby, ignoruj
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'invoice_number')) {
            try {
                Schema::table('invoices', function (Blueprint $table) {
                    // Vrátime späť na povinné pole
                    $table->string('invoice_number')->nullable(false)->change();
                });
            } catch (\Exception $e) {
                // V prípade chyby pri rollback, ignoruj
            }
        }
    }
}; 