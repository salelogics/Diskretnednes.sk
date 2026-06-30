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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique(); // Číslo faktúry (napr. 2025001)
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Zákazník
                $table->foreignId('ad_payment_id')->nullable()->constrained()->onDelete('set null'); // Platba
                
                // Základné údaje faktúry
                $table->date('issue_date'); // Dátum vystavenia
                $table->date('due_date'); // Dátum splatnosti
                $table->date('delivery_date'); // Dátum dodania
                $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
                
                // Sumy
                $table->decimal('subtotal', 10, 2); // Suma bez DPH
                $table->decimal('tax_amount', 10, 2)->default(0); // Suma DPH
                $table->decimal('total_amount', 10, 2); // Suma s DPH
                
                // Údaje o predajcovi
                $table->string('seller_name');
                $table->string('seller_address');
                $table->string('seller_city');
                $table->string('seller_zip');
                $table->string('seller_country');
                $table->string('seller_tax_id')->nullable();
                $table->string('seller_vat_id')->nullable();
                $table->string('seller_phone')->nullable();
                $table->string('seller_email')->nullable();
                
                // Údaje o kupujúcom
                $table->string('buyer_name');
                $table->string('buyer_address');
                $table->string('buyer_city');
                $table->string('buyer_zip');
                $table->string('buyer_country');
                $table->string('buyer_tax_id')->nullable();
                $table->string('buyer_vat_id')->nullable();
                $table->string('buyer_phone')->nullable();
                $table->string('buyer_email')->nullable();
                
                // Dodatočné informácie
                $table->text('notes')->nullable(); // Poznámky
                $table->string('payment_method')->nullable(); // Spôsob platby
                $table->string('currency', 3)->default('EUR'); // Mena
                $table->decimal('exchange_rate', 10, 4)->default(1.0000); // Kurz
                
                $table->timestamps();
                
                // Indexy
                $table->index(['user_id']);
                $table->index(['status']);
                $table->index(['issue_date']);
                $table->index(['due_date']);
                $table->index(['invoice_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
