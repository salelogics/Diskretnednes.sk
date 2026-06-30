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
        if (!Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->string('session_id')->nullable(); // Pre neprihlásených používateľov
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Pre prihlásených
                $table->foreignId('ad_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                // Zabezpečenie unikátnosti - jeden používateľ/session môže mať jeden inzerát len raz v obľúbených
                $table->unique(['session_id', 'ad_id']);
                $table->unique(['user_id', 'ad_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
