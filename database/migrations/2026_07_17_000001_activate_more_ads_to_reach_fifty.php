<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DISABLED FOR PRODUCTION (2026-08-24 PROD-readiness audit).
     *
     * This originally topped up the number of publicly visible ads to an
     * arbitrary target of 50 by activating real 'draft'/'pending' ads,
     * creating AdPayment rows and emailing owners via markAsCompleted().
     * That made sense to populate the dev/demo environment, but running it
     * against a real production database would publish ads real users
     * haven't finished or that are still awaiting moderation, and send
     * "payment received" emails for payments nobody made.
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
