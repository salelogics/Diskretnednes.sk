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
            $table->unsignedBigInteger('ad_id')->nullable()->after('id');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->index(['ad_id', 'verification_code'], 'sms_verifications_ad_code_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_verifications', function (Blueprint $table) {
            $table->dropIndex('sms_verifications_ad_code_index');
            $table->dropForeign(['ad_id']);
            $table->dropColumn('ad_id');
        });
    }
};
