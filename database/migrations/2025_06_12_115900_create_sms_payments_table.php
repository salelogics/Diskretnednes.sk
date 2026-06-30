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
        if (!Schema::hasTable('sms_payments')) {
            Schema::create('sms_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_id', 20)->unique(); // ID platby
                $table->string('sms_id', 20)->nullable(); // ID SMS správy (offline)
                $table->string('type')->default('online'); // online/offline
                $table->string('description', 30); // Popis platby
                $table->decimal('price', 8, 2); // Cena
                $table->string('msisdn', 15)->nullable(); // Telefónne číslo
                $table->string('status')->default('pending'); // pending, success, failed, timeout
                $table->string('result')->nullable(); // OK, FAIL, TIMEOUT
                $table->text('sms_text')->nullable(); // Text SMS (offline)
                $table->text('response_message')->nullable(); // Odpoveď pre zákazníka
                $table->timestamps();
                
                $table->index(['payment_id']);
                $table->index(['sms_id']);
                $table->index(['status']);
                $table->index(['type']);
                $table->index(['msisdn']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_payments');
    }
};
