<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // payment, ad, support, system, moderation
                $table->string('title');
                $table->text('message');
                $table->string('icon')->nullable(); // ri-notification-line, ri-money-dollar-circle-line, atď.
                $table->string('color')->default('blue'); // blue, green, red, yellow, purple
                $table->json('data')->nullable(); // dodatočné dáta (ID inzerátu, platby, atď.)
                $table->string('action_url')->nullable(); // URL kam má notifikácia viesť
                $table->string('action_text')->nullable(); // text tlačidla (Zobraziť, Platiť, atď.)
                $table->boolean('is_read')->default(false);
                $table->boolean('is_admin')->default(false); // či je to admin notifikácia
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->timestamp('expires_at')->nullable(); // kedy notifikácia expiruje
                $table->timestamps();
                
                $table->index(['user_id', 'is_read']);
                $table->index(['type']);
                $table->index(['priority']);
                $table->index(['expires_at']);
                $table->index(['is_admin']);
                $table->index(['created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}; 