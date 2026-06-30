<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('erotic_clubs') && Schema::hasColumn('erotic_clubs', 'working_hours')) {
            // Použijeme raw SQL, aby sme sa vyhli potrebe doctrine/dbal
            DB::statement('ALTER TABLE erotic_clubs MODIFY working_hours TEXT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('erotic_clubs') && Schema::hasColumn('erotic_clubs', 'working_hours')) {
            DB::statement('ALTER TABLE erotic_clubs MODIFY working_hours TEXT NOT NULL');
        }
    }
};


