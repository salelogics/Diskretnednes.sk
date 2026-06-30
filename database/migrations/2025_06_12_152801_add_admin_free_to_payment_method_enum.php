<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pre MySQL musíme upraviť enum cez ALTER TABLE
        DB::statement("ALTER TABLE ad_payments MODIFY COLUMN payment_method ENUM('bank_transfer', 'stripe', 'qr_code', 'sms', 'admin_free')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Vrátime späť pôvodný enum
        DB::statement("ALTER TABLE ad_payments MODIFY COLUMN payment_method ENUM('bank_transfer', 'stripe', 'qr_code', 'sms')");
    }
};
