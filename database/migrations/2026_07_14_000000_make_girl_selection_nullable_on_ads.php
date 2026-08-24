<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The "Výber dievčat" (girl_selection) and "Skúsenosti" (experience) fields
     * were removed from the ad forms. `experience` is already nullable; make
     * `girl_selection` nullable too so new ads can be created without it.
     * Existing data in both columns is left untouched.
     */
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'girl_selection')) {
                $table->string('girl_selection')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'girl_selection')) {
                $table->string('girl_selection')->nullable(false)->change();
            }
        });
    }
};
