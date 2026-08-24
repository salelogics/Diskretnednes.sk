<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Vyber dievčat" (girl_selection) and "Skúsenosti" (experience) were
     * removed from the create/edit ad forms per spec, so AdsController and
     * AdminAdsController's store() no longer supply values for either
     * column - the INSERT statement simply omits them.
     *
     * The columns were added via `->string(...)->nullable()` in
     * 2025_07_27_151121 / 2025_07_27_220022, guarded by hasColumn() checks
     * that only ADD the column if missing - they never touch an
     * already-existing column. On this environment `experience` (and
     * possibly `girl_selection`) ended up NOT NULL with no default, which
     * is why every ad creation started failing with:
     * "SQLSTATE[HY000]: General error: 1364 Field 'experience' doesn't
     * have a default value".
     *
     * Uses raw SQL (not Schema::change()) since doctrine/dbal isn't in
     * composer.lock.
     */
    public function up(): void
    {
        foreach (['girl_selection', 'experience'] as $column) {
            if (Schema::hasColumn('ads', $column)) {
                DB::statement("ALTER TABLE `ads` MODIFY `{$column}` VARCHAR(255) NULL DEFAULT NULL");
            }
        }
    }

    public function down(): void
    {
        // Intentionally left nullable - these fields are gone from the UI,
        // reverting to NOT NULL would just reintroduce the bug.
    }
};
