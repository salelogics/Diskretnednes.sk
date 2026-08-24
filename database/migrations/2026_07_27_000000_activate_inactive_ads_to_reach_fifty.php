<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DISABLED FOR PRODUCTION (2026-08-24 PROD-readiness audit).
     *
     * This originally topped up visible ads to 50 by also drawing from
     * 'inactive' ads (deliberately paused by their owner or an admin), on
     * top of 'draft'/'pending'. On a real production database this would
     * reactivate ads whose owner or an admin made a deliberate decision to
     * pause, without their consent - the highest-risk of the top-up
     * migrations. Disabled for the same reason as 2026_07_17_000001, plus
     * this additional concern.
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
