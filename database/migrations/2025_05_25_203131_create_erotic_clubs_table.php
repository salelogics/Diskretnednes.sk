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
        if (!Schema::hasTable('erotic_clubs')) {
            Schema::create('erotic_clubs', function (Blueprint $table) {
                $table->id();
                
                // Základné údaje
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('logo_path')->nullable();
                $table->string('image_path')->nullable();
                $table->text('description');
                
                // Kontaktné údaje
                $table->string('address');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                
                // Pracovné hodiny (povoliť NULL kvôli admin tvorbe bez zadania)
                $table->text('working_hours')->nullable();
                
                // Služby
                $table->text('services');
                
                // Hodnotenie
                $table->decimal('rating', 3, 2)->default(0.00);
                $table->integer('reviews_count')->default(0);
                
                // Ceny
                $table->text('pricing');
                
                // Štatistiky
                $table->integer('views')->default(0);
                $table->integer('phone_views')->default(0);
                
                // Poloha
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                
                // Status
                $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
                $table->boolean('is_featured')->default(false);
                
                // WordPress polia
                $table->unsignedBigInteger('wp_id')->nullable();
                $table->timestamp('wp_modified_at')->nullable();
                
                $table->timestamps();
                
                // Indexy
                $table->index(['status', 'is_featured']);
                $table->index(['rating']);
                $table->index(['views']);
                $table->index(['slug']);
                $table->index('wp_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erotic_clubs');
    }
};
