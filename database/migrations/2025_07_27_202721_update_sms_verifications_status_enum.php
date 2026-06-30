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
        // Použijem raw SQL na aktualizáciu enum stĺpca
        DB::statement("ALTER TABLE sms_verifications MODIFY COLUMN status ENUM(
            'waiting_for_sms',
            'pending', 
            'used', 
            'expired',
            'verified',
            'failed',
            'cancelled'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverz - vrátime späť na starý enum
        // Najskôr aktualizujeme hodnoty ktoré nebudú podporované
        DB::statement("UPDATE sms_verifications SET status = 'pending' 
                      WHERE status IN ('waiting_for_sms', 'verified', 'failed', 'cancelled')");
        
        // Potom zmeníme enum späť na pôvodné hodnoty
        DB::statement("ALTER TABLE sms_verifications MODIFY COLUMN status ENUM(
            'pending', 
            'used', 
            'expired'
        ) DEFAULT 'pending'");
    }
};
