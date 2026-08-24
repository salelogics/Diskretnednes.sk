<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DISABLED FOR PRODUCTION (2026-08-24 PROD-readiness audit).
     *
     * This originally ran `php artisan ads:top-up --target=50` during
     * migrate, which activates draft/pending/inactive ads (including
     * reactivating deliberately-paused ones) to reach an arbitrary
     * visible-ad count. Same production risk as the other top-up
     * migrations - see 2026_07_17_000001's docblock. The `ads:top-up`
     * artisan command itself is untouched and still runnable by hand over
     * SSH if ever needed; only the automatic invocation during migrate is
     * disabled here.
     *
     * The original implementation is preserved in this file's git history
     * (see `git log -p` on this path) rather than duplicated here.
     */
    public function up(): void
    {
        // Intentionally a no-op on production. See docblock above.
    }

    /**
     * Already intentionally irreversible before this was disabled - we never
     * recorded which ads were activated here versus already active.
     */
    public function down(): void
    {
    }
};
