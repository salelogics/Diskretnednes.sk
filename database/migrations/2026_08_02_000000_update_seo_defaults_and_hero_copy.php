<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * seo_default_title/seo_default_description are only ever written through
     * the admin SEO panel and override config/seo.php on every request
     * (AppServiceProvider). Client asked for specific new copy, so set it
     * directly here rather than relying on config/seo.php alone - otherwise
     * a previously admin-saved value (even one already scrubbed of
     * "erotikon" by an earlier migration) would keep showing old wording.
     */
    public function up(): void
    {
        $replacements = [
            'seo_default_title' => 'DiskretneDnes.sk – diskrétne zoznamovanie a stretnutia pre dospelých',
            'seo_default_description' => 'Objavte diskrétne zoznamovanie a stretnutia pre dospelých na Slovensku. Nájdite ľudí podľa lokality a vašich predstáv. Súkromne a diskrétne.',
        ];

        foreach ($replacements as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'description' => $key, 'type' => 'string', 'updated_at' => now(), 'created_at' => now()]
            );
            Cache::forget("setting_{$key}");
        }

        Cache::forget('all_settings');
    }

    public function down(): void
    {
        //
    }
};
