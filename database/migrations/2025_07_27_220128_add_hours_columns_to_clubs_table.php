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
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'hours_weekdays')) {
                $table->string('hours_weekdays')->nullable()->after('website');
            }
            
            if (!Schema::hasColumn('clubs', 'hours_weekend')) {
                $table->string('hours_weekend')->nullable()->after('hours_weekdays');
            }
            
            if (!Schema::hasColumn('clubs', 'hours_sunday')) {
                $table->string('hours_sunday')->nullable()->after('hours_weekend');
            }
            
            if (!Schema::hasColumn('clubs', 'slug')) {
                $table->string('slug')->nullable()->after('hours_sunday');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $columnsToRemove = ['hours_weekdays', 'hours_weekend', 'hours_sunday', 'slug'];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('clubs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
