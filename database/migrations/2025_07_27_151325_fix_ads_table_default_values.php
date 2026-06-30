<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Nastavíme predvolené hodnoty pre text polia na NULL
        $textColumns = ['services', 'location', 'prices', 'working_hours', 'additional_info'];
        
        foreach ($textColumns as $column) {
            if (Schema::hasColumn('ads', $column)) {
                DB::statement("ALTER TABLE ads MODIFY COLUMN {$column} TEXT NULL DEFAULT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes if needed
        $textColumns = ['services', 'location', 'prices', 'working_hours', 'additional_info'];
        
        foreach ($textColumns as $column) {
            if (Schema::hasColumn('ads', $column)) {
                DB::statement("ALTER TABLE ads MODIFY COLUMN {$column} TEXT NOT NULL");
            }
        }
    }
};
