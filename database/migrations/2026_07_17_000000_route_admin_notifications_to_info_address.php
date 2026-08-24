<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The contact form (and other admin notifications) deliver to whatever
     * the `admin_notification_emails` setting resolves to. The rebrand
     * migration (2026_07_15_000003) pointed it at admin@diskretnednes.sk,
     * but the /kontakt page promises info@diskretnednes.sk as the way to
     * reach the site - those need to match. Route it to info@ instead.
     *
     * Only touches the row if it still holds the old erotikon value or the
     * admin@ address this correction is meant to fix, so an admin who has
     * since customized it via the settings panel to something else is left
     * alone.
     */
    public function up(): void
    {
        DB::table('settings')
            ->where('key', 'admin_notification_emails')
            ->where(function ($query) {
                $query->where('value', 'like', '%erotikon%')
                    ->orWhere('value', 'admin@diskretnednes.sk');
            })
            ->update(['value' => 'info@diskretnednes.sk']);
    }

    public function down(): void
    {
        // Intentionally irreversible - we don't know each row's original value.
    }
};
