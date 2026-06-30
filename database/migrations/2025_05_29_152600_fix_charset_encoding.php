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
        // Oprava charset pre všetky tabuľky
        $tables = [
            'erotic_clubs',
            'ads', 
            'users',
            'blog_posts',
            'articles'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }

        // Oprava konkrétnych problémových textov
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'á') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'é') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'í') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ó') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ú') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ý') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ž') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'š') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'č') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ť') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ň') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ľ') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ô') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ä') WHERE name LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET name = REPLACE(name, '?', 'ď') WHERE name LIKE '%?%'");

        // Rovnaké opravy pre description
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'á') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'é') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'í') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ó') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ú') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ý') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ž') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'š') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'č') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ť') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ň') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ľ') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ô') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ä') WHERE description LIKE '%?%'");
        DB::statement("UPDATE erotic_clubs SET description = REPLACE(description, '?', 'ď') WHERE description LIKE '%?%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback nie je potrebný
    }
};
