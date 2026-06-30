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
        if (!Schema::hasTable('payment_packages')) {
            Schema::create('payment_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Classic, Premium
                $table->string('type'); // classic, premium
                $table->integer('duration_days'); // 5, 7, 30, 90, 365
                $table->decimal('price', 8, 2); // Cena v eurách
                $table->boolean('is_featured')->default(false); // Zvýraznený
                $table->boolean('is_top_ad')->default(false); // Topovaný
                $table->text('description')->nullable();
                $table->json('features')->nullable(); // Zoznam funkcií
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index(['type', 'is_active']);
                $table->index(['is_active', 'sort_order']);
                $table->index(['duration_days']);
                $table->index(['price']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_packages');
    }
};
