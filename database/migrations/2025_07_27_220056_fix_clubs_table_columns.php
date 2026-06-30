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
        Schema::table('erotic_clubs', function (Blueprint $table) {
            // Pridáme chýbajúce stĺpce ktoré seeder očakáva
            
            if (!Schema::hasColumn('erotic_clubs', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('erotic_clubs', 'hours_weekdays')) {
                $table->string('hours_weekdays')->nullable()->after('working_hours');
            }
            
            if (!Schema::hasColumn('erotic_clubs', 'hours_weekend')) {
                $table->string('hours_weekend')->nullable()->after('hours_weekdays');
            }
            
            if (!Schema::hasColumn('erotic_clubs', 'hours_sunday')) {
                $table->string('hours_sunday')->nullable()->after('hours_weekend');
            }
            
            if (!Schema::hasColumn('erotic_clubs', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erotic_clubs', function (Blueprint $table) {
            $columnsToRemove = ['city', 'hours_weekdays', 'hours_weekend', 'hours_sunday', 'is_active'];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('erotic_clubs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
