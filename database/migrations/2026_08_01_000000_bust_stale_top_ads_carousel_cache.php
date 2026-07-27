<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * One-off: the homepage top-ads carousel is cached for 30 minutes under
     * top_ads_carousel_v3 (PublicAdsController), and nothing invalidated it
     * when an ad's status/subscription/top_ad/featured flags changed - a
     * deactivated ad could keep showing in the carousel until the cache
     * naturally expired. The Ad model now busts this cache on every
     * relevant save, but that only prevents it going forward; this clears
     * whatever stale entry is already sitting in the cache right now.
     */
    public function up(): void
    {
        Cache::forget('top_ads_carousel_v3');
    }

    public function down(): void
    {
        //
    }
};
