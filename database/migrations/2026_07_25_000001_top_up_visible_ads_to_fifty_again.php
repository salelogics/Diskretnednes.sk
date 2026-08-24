<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DISABLED FOR PRODUCTION (2026-08-24 PROD-readiness audit).
     *
     * Duplicate of 2026_07_17_000001's dev/demo top-up-to-50 logic (same
     * risk: activates real draft/pending ads, creates AdPayment rows, sends
     * "payment received" emails via markAsCompleted()). Not appropriate for
     * a real production database - see that migration's docblock for the
     * full reasoning, which applies here identically.
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
     * recorded which ads the original up() touched vs. were already active.
     */
    public function down(): void
    {
    }
};
