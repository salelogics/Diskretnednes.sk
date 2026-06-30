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
        if (!Schema::hasTable('sms_verifications')) {
            Schema::create('sms_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('msisdn', 15); // Telefónne číslo zákazníka
                $table->string('sms_id', 50); // ID SMS správy z PlatbaMobilom.sk
                $table->string('verification_code', 6); // 6-miestny verifikačný kód
                $table->string('package_type', 20); // classic alebo premium
                $table->integer('package_duration'); // počet dní
                $table->decimal('price', 8, 2); // cena platby
                $table->string('sms_code', 10); // SMS kód (napr. EROFOX)
                $table->boolean('is_verified')->default(false); // či bol kód overený
                $table->boolean('is_payment_confirmed')->default(false); // či bola platba potvrdená
                $table->timestamp('expires_at'); // kedy vyprší verifikačný kód
                $table->timestamp('verified_at')->nullable(); // kedy bol overený
                $table->timestamps();
                
                $table->index(['msisdn', 'verification_code']);
                $table->index(['sms_id']);
                $table->index(['expires_at']);
                $table->index(['is_verified', 'is_payment_confirmed']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_verifications');
    }
};
