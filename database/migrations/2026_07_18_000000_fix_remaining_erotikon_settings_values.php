<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * seo_default_title / seo_default_description are only ever written
     * through the admin SEO settings panel (AdminSeoController::update) -
     * they're not seeded by any migration, so the earlier rebrand
     * migration (2026_07_15_000003) never touched them and an admin-saved
     * "Erotikon.sk" title/description kept overriding config/seo.php via
     * AppServiceProvider on every request.
     *
     * Rather than hardcode just those two keys, this sweeps every settings
     * row still containing "erotikon" and swaps the brand strings in place,
     * preserving whatever surrounding copy an admin wrote instead of
     * overwriting the whole value.
     */
    public function up(): void
    {
        $rows = DB::table('settings')->where('value', 'like', '%erotikon%')->get();

        $replacements = [
            'https://www.erotikon.sk' => 'https://www.diskretnednes.sk',
            'https://erotikon.sk' => 'https://diskretnednes.sk',
            'www.erotikon.sk' => 'www.diskretnednes.sk',
            'Erotikon.sk' => 'Diskretnednes.sk',
            'EROTIKON.SK' => 'DISKRETNEDNES.SK',
            'erotikon.sk' => 'diskretnednes.sk',
            'Erotikon' => 'Diskrétne Dnes',
            'erotikon' => 'diskretnednes',
        ];

        foreach ($rows as $row) {
            $value = $row->value;

            foreach ($replacements as $search => $replace) {
                $value = str_replace($search, $replace, $value);
            }

            if ($value !== $row->value) {
                DB::table('settings')->where('id', $row->id)->update(['value' => $value]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally irreversible - original admin-entered values aren't recorded.
    }
};
