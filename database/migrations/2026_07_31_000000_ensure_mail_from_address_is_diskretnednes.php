<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Safety net for the 2026_07_15 rebrand migration: that one only fixed
     * mail_from_address/mail_from_name if the row already existed and still
     * contained "erotikon". If the row was empty/missing, emails kept using
     * mail_username (the raw SMTP login mailbox, still info@erotikon.sk from
     * before the rebrand) as the From address - see AppServiceProvider,
     * which used to copy mail_username into mail.from.address unconditionally.
     * This ensures the From address/name are correct regardless of that row's
     * prior state, and busts the settings cache so it takes effect immediately.
     */
    public function up(): void
    {
        $replacements = [
            'mail_from_address' => 'info@diskretnednes.sk',
            'mail_from_name' => 'DiskretneDnes.sk',
        ];

        foreach ($replacements as $key => $newValue) {
            $existing = DB::table('settings')->where('key', $key)->first();

            if (!$existing || empty($existing->value) || str_contains($existing->value, 'erotikon')) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $newValue, 'description' => $key, 'type' => 'string', 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        Cache::forget('all_settings');
        Cache::forget('setting_mail_from_address');
        Cache::forget('setting_mail_from_name');
    }

    public function down(): void
    {
        // Intentionally irreversible: we don't know each row's original value.
    }
};
