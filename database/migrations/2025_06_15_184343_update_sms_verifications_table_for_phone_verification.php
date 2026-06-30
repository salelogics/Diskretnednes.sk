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
        // Kontrola či tabuľka existuje
        if (!Schema::hasTable('sms_verifications')) {
            return;
        }
        
        Schema::table('sms_verifications', function (Blueprint $table) {
            // Pridaj user_id iba ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Pridaj phone_number iba ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            
            // Pridaj sms_keyword iba ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'sms_keyword')) {
                $table->string('sms_keyword', 20)->default('VERIFY');
            }
            
            // Pridaj status iba ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'status')) {
                $table->enum('status', ['pending', 'verified', 'expired', 'failed'])->default('pending');
            }
            
            // Pridaj transaction_id iba ak neexistuje
            if (!Schema::hasColumn('sms_verifications', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }
            
            // PRESKOČÍM verified_at - už existuje v tabuľke
            // if (!Schema::hasColumn('sms_verifications', 'verified_at')) {
            //     $table->timestamp('verified_at')->nullable();
            // }
        });
        
        // Pridaj indexy s kontrolou duplicity
        Schema::table('sms_verifications', function (Blueprint $table) {
            try {
                $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM sms_verifications WHERE Key_name = 'sms_verifications_phone_number_status_index'");
                if (empty($indexes)) {
                    $table->index(['phone_number', 'status']);
                }
            } catch (\Exception $e) {
                // Index už existuje alebo iný problém, ignoruj
            }
            
            try {
                $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM sms_verifications WHERE Key_name = 'sms_verifications_verification_code_expires_at_index'");
                if (empty($indexes)) {
                    $table->index(['verification_code', 'expires_at']);
                }
            } catch (\Exception $e) {
                // Index už existuje alebo iný problém, ignoruj
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('sms_verifications')) {
            return;
        }
        
        Schema::table('sms_verifications', function (Blueprint $table) {
            // Odstraň foreign key ak existuje
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {
                // Foreign key neexistuje, ignoruj
            }
            
            // Odstraň indexy ak existujú
            try {
                $table->dropIndex(['phone_number', 'status']);
            } catch (\Exception $e) {
                // Index neexistuje, ignoruj
            }
            
            try {
                $table->dropIndex(['verification_code', 'expires_at']);
            } catch (\Exception $e) {
                // Index neexistuje, ignoruj
            }
            
            // Odstraň stĺpce ak existujú
            $columnsToRemove = [];
            
            if (Schema::hasColumn('sms_verifications', 'user_id')) {
                $columnsToRemove[] = 'user_id';
            }
            
            if (Schema::hasColumn('sms_verifications', 'phone_number')) {
                $columnsToRemove[] = 'phone_number';
            }
            
            if (Schema::hasColumn('sms_verifications', 'sms_keyword')) {
                $columnsToRemove[] = 'sms_keyword';
            }
            
            if (Schema::hasColumn('sms_verifications', 'status')) {
                $columnsToRemove[] = 'status';
            }
            
            if (Schema::hasColumn('sms_verifications', 'transaction_id')) {
                $columnsToRemove[] = 'transaction_id';
            }
            
            // PRESKOČÍME verified_at - nech zostane v tabuľke
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
