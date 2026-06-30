<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added missing import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sms_verifications', function (Blueprint $table) {
            // Pridáme kolumnu status ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'status')) {
                $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            }
            
            // Pridáme kolumnu used_at ak neexistuje  
            if (!Schema::hasColumn('sms_verifications', 'used_at')) {
                $table->timestamp('used_at')->nullable();
            }
            
            // Pridáme kolumnu package_code ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'package_code')) {
                $table->string('package_code', 10)->nullable();
            }
        });
        
        // Ak máme staré záznamy, nastavíme im status na základe is_verified
        DB::statement("UPDATE sms_verifications SET status = CASE 
            WHEN is_verified = 1 THEN 'used' 
            WHEN expires_at < NOW() THEN 'expired'
            ELSE 'pending' 
        END WHERE status IS NULL OR status = 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_verifications', function (Blueprint $table) {
            $table->dropColumn(['status', 'used_at', 'package_code']);
        });
    }
};
