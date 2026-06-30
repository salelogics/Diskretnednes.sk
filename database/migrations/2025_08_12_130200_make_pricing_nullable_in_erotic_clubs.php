<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('erotic_clubs') && Schema::hasColumn('erotic_clubs', 'pricing')) {
            DB::statement('ALTER TABLE erotic_clubs MODIFY pricing TEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('erotic_clubs') && Schema::hasColumn('erotic_clubs', 'pricing')) {
            DB::statement('ALTER TABLE erotic_clubs MODIFY pricing TEXT NOT NULL');
        }
    }
};


