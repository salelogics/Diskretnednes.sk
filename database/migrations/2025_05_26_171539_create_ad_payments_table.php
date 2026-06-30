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
        if (!Schema::hasTable('ad_payments')) {
            Schema::create('ad_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('ad_id')->constrained()->onDelete('cascade');
                $table->foreignId('payment_package_id')->constrained()->onDelete('cascade');
                
                // Platobné údaje
                $table->string('payment_id')->unique(); // Unikátne ID platby
                $table->decimal('amount', 8, 2); // Suma platby
                $table->string('currency', 3)->default('EUR');
                $table->enum('payment_method', ['bank_transfer', 'stripe', 'qr_code', 'sms', 'admin_free']); // Spôsob platby
                $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
                
                // Dátumy
                $table->timestamp('valid_from')->nullable(); // Odkedy platí
                $table->timestamp('valid_until')->nullable(); // Dokedy platí
                $table->timestamp('paid_at')->nullable(); // Kedy zaplatené
                
                // Stripe údaje
                $table->string('stripe_payment_intent_id')->nullable();
                $table->string('stripe_session_id')->nullable();
                $table->json('stripe_metadata')->nullable();
                
                // QR kód údaje
                $table->string('qr_code_id')->nullable();
                $table->string('qr_code_url')->nullable();
                
                // SMS platba údaje
                $table->string('sms_verification_id')->nullable();
                $table->string('sms_phone_number')->nullable();
                
                // Bankový prevod údaje
                $table->string('bank_reference')->nullable();
                $table->text('bank_instructions')->nullable();
                
                // Faktúra
                $table->string('invoice_number')->nullable();
                $table->string('invoice_path')->nullable();
                
                // Metadata
                $table->json('payment_data')->nullable(); // Surové dáta z platobnej brány
                $table->text('notes')->nullable(); // Poznámky
                
                $table->timestamps();
                
                // Indexy
                $table->index(['user_id', 'status']);
                $table->index(['ad_id', 'status']);
                $table->index(['payment_method']);
                $table->index(['status']);
                $table->index(['valid_from', 'valid_until']);
                $table->index(['paid_at']);
                $table->index(['created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_payments');
    }
};
