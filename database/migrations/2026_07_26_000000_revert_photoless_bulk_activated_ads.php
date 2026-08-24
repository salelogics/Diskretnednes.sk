<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DISABLED FOR PRODUCTION (2026-08-24 PROD-readiness audit).
     *
     * This originally (1) reverted photo-less ads activated by the
     * 2026_07_17_000001/2026_07_25_000001 bulk top-ups, and (2) topped the
     * visible-ad count back up to 50 from photo-having draft/pending ads,
     * creating AdPayment rows and emailing owners via markAsCompleted().
     * Same production risk as those two migrations: it would activate real
     * users' unfinished or unmoderated ads on a real database. Disabled for
     * the same reason - see 2026_07_17_000001's docblock for the full
     * reasoning.
     *
     * The original implementation is preserved in this file's git history
     * (see `git log -p` on this path) rather than duplicated here.
     */
    public function up(): void
    {
        // Intentionally a no-op on production. See docblock above.
    }

    /**
     * Already intentionally irreversible before this was disabled.
     */
    public function down(): void
    {
    }
};
