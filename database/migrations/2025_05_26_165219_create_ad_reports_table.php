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
        if (!Schema::hasTable('ad_reports')) {
            Schema::create('ad_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ad_id')->constrained()->onDelete('cascade');
                $table->string('reason');
                $table->text('details')->nullable();
                $table->string('reporter_ip');
                $table->string('reporter_email')->nullable();
                $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                
                $table->index(['ad_id', 'status']);
                $table->index(['status']);
                $table->index(['created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_reports');
    }
};
