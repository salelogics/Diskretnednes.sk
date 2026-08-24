<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rebrand: updates already-seeded `settings` rows that still hold the old
     * Erotikon branding/domain, on installs where the earlier settings
     * migrations already ran (so editing those migration files alone has no
     * effect on live data). Only touches a row if its current value still
     * contains "erotikon" - if an admin already customized a setting via the
     * settings panel to something else, it is left untouched.
     */
    public function up(): void
    {
        $replacements = [
            'app_name' => 'Diskrétne Dnes',
            'mail_from_name' => 'DiskretneDnes.sk',
            'mail_from_address' => 'info@diskretnednes.sk',
            'admin_notification_emails' => 'admin@diskretnednes.sk',
            'app_url' => 'https://diskretnednes.sk',
            'seo_default_image' => 'images/uploads/diskretne-dnes-logo-black.png',
        ];

        foreach ($replacements as $key => $newValue) {
            DB::table('settings')
                ->where('key', $key)
                ->where('value', 'like', '%erotikon%')
                ->update(['value' => $newValue]);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: we don't know each row's original
        // value, and reverting a live rebrand automatically is not safe.
    }
};
