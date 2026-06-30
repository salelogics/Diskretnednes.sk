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
        Schema::table('sms_verifications', function (Blueprint $table) {
            // Zmeníme msisdn na nullable - telefónne číslo sa pridá až keď používateľ pošle SMS
            $table->string('msisdn', 15)->nullable()->change();
            
            // Zmeníme aj sms_id na nullable - SMS ID sa pridá až keď príde SMS od používateľa
            $table->string('sms_id', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_verifications', function (Blueprint $table) {
            // Reverz - vrátime stĺpce späť na NOT NULL
            // Pozor: toto môže zlyhať ak v databáze sú NULL hodnoty
            $table->string('msisdn', 15)->nullable(false)->change();
            $table->string('sms_id', 50)->nullable(false)->change();
        });
    }
};
