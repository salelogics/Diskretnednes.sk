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
        if (!Schema::hasTable('sms_payment_verifications')) {
            Schema::create('sms_payment_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('msisdn', 15); // Telefónne číslo
                $table->string('sms_id', 50); // ID SMS správy
                $table->string('verification_code', 6); // 6-miestny verifikačný kód
                $table->string('sms_code', 10); // SMS kód (FXO, CC3, atď.)
                $table->string('package_type', 20); // classic, premium, gold
                $table->integer('package_duration'); // počet dní
                $table->decimal('price', 8, 2); // Cena
                $table->boolean('is_verified')->default(false);
                $table->timestamp('expires_at'); // Kedy verifikačný kód vyprší
                $table->timestamp('verified_at')->nullable(); // Kedy bol overený
                $table->timestamps();
                
                $table->index(['msisdn', 'verification_code']);
                $table->index(['sms_id', 'verification_code']);
                $table->index(['is_verified', 'expires_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_payment_verifications');
    }
};
