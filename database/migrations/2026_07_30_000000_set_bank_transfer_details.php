<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DISABLED FOR PRODUCTION (2026-08-24 PROD-readiness audit).
     *
     * This originally filled empty invoice_company_name/invoice_bank_iban/
     * invoice_bank_swift settings with "3DIVISION s.r.o." and a real IBAN/
     * SWIFT, for bank-transfer Premium payments. Payments are currently
     * turned off site-wide specifically because no s.r.o. is available to
     * invoice through yet, and this company must not appear anywhere on the
     * production site or database. Disabled entirely rather than made
     * conditional, since there is currently no correct value to insert here.
     *
     * The original implementation is preserved in this file's git history
     * (see `git log -p` on this path) rather than duplicated here.
     */
    public function up(): void
    {
        // Intentionally a no-op on production. See docblock above.
    }

    public function down(): void
    {
        // Already a no-op before this was disabled.
    }
};
