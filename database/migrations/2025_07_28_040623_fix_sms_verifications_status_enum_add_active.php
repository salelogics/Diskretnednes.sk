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
        // Pridáme hodnotu 'active' do status ENUM
        DB::statement("ALTER TABLE sms_verifications MODIFY COLUMN status ENUM('pending', 'waiting_for_sms', 'verified', 'expired', 'cancelled', 'active') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Odstránime hodnotu 'active' z status ENUM
        DB::statement("ALTER TABLE sms_verifications MODIFY COLUMN status ENUM('pending', 'waiting_for_sms', 'verified', 'expired', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
