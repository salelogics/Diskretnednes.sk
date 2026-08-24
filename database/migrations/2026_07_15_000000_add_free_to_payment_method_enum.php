<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds 'free' to ad_payments.payment_method, used for the self-service
     * free Classic package (distinct from 'admin_free', which marks a
     * subscription an admin granted manually).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE ad_payments MODIFY COLUMN payment_method ENUM('bank_transfer', 'stripe', 'qr_code', 'sms', 'admin_free', 'free')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ad_payments MODIFY COLUMN payment_method ENUM('bank_transfer', 'stripe', 'qr_code', 'sms', 'admin_free')");
    }
};
