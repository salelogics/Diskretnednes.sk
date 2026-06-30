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
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique(); // napr. 'welcome', 'ad_created', atd.
                $table->string('name'); // Popisný názov
                $table->string('subject'); // Predmet emailu
                $table->text('content'); // HTML obsah emailu
                $table->text('variables')->nullable(); // JSON s dostupnými premennými
                $table->enum('type', ['user', 'admin'])->default('user'); // Typ emailu
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
}; 