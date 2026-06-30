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
        if (!Schema::hasTable('ads')) {
            Schema::create('ads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                
                // Základné informácie
                $table->string('ad_type'); // zena, muz, par, trans, klub
                $table->string('nationality');
                $table->integer('age');
                $table->string('city');
                $table->string('street')->nullable();
                $table->string('offer_type'); // ponukam-privat, ponukam-escort, atď.
                $table->string('girl_selection'); // som-uplne-sama, viac-dievcat, atď.
                $table->string('phone');
                $table->string('whatsapp')->nullable();
                $table->string('viber')->nullable();
                $table->string('telegram')->nullable();
                $table->string('email')->nullable();
                $table->text('description');
                $table->text('services');
                $table->text('location')->nullable();
                $table->text('prices');
                $table->text('working_hours');
                $table->text('additional_info')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_fake')->default(false);
                $table->boolean('is_agency')->default(false);
                
                // Moderácia
                $table->enum('status', ['draft', 'pending', 'active', 'inactive', 'rejected'])->default('draft');
                $table->text('rejection_reason')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                
                // Štatistiky
                $table->integer('views')->default(0);
                $table->integer('phone_views')->default(0);
                $table->integer('whatsapp_views')->default(0);
                $table->integer('viber_views')->default(0);
                $table->integer('telegram_views')->default(0);
                $table->integer('favorites_count')->default(0);
                
                // Obrázky
                $table->string('gallery_image_1')->nullable();
                $table->string('gallery_image_2')->nullable();
                $table->string('gallery_image_3')->nullable();
                $table->string('gallery_image_4')->nullable();
                $table->string('gallery_image_5')->nullable();
                $table->string('gallery_image_6')->nullable();
                $table->string('gallery_image_7')->nullable();
                $table->string('gallery_image_8')->nullable();
                $table->string('gallery_image_9')->nullable();
                $table->string('gallery_image_10')->nullable();
                $table->string('verification_image')->nullable();
                
                $table->timestamps();
                
                // Indexy
                $table->index(['status', 'created_at']);
                $table->index(['user_id', 'status']);
                $table->index(['ad_type', 'city', 'status']);
                $table->index(['nationality', 'status']);
                $table->index(['age', 'status']);
                $table->index(['expires_at']);
                $table->index(['is_verified', 'status']);
                $table->index(['views']);
                $table->index(['phone_views']);
                $table->index(['favorites_count']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
