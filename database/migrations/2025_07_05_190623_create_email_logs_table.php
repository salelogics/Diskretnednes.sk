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
        if (!Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->string('to_email');
                $table->string('from_email')->nullable();
                $table->string('subject');
                $table->text('body')->nullable();
                $table->string('type')->default('general'); // general, welcome, notification, support, payment, etc.
                $table->enum('status', ['sent', 'failed', 'queued'])->default('sent');
                $table->text('error_message')->nullable();
                $table->json('headers')->nullable();
                $table->json('metadata')->nullable(); // dodatočné informácie (user_id, ad_id, etc.)
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
                
                $table->index(['to_email', 'created_at']);
                $table->index(['type', 'created_at']);
                $table->index(['status', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
