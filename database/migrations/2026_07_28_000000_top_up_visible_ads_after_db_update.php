<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * The database was updated with more deactivated ads after the previous
     * top-up migrations had already run - and a migration only ever runs
     * once, so none of them can pick those up.
     *
     * Delegates to `ads:top-up`, which holds the logic and can be re-run by
     * hand any time the data changes again, instead of needing a new
     * migration each round.
     */
    public function up(): void
    {
        Artisan::call('ads:top-up', ['--target' => 50]);
    }

    /**
     * Intentionally irreversible - we don't record which ads were activated
     * here versus already active.
     */
    public function down(): void
    {
    }
};
