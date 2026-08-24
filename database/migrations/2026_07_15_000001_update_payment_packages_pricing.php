<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * New pricing per spec:
     * - Classic: free, no time limit (duration_days = 0 is treated as "no expiry"
     *   by AdPayment::markAsCompleted()).
     * - Premium topované: only two tiers - 10 days / 10 EUR and 30 days / 20 EUR.
     *
     * The previous 12 packages (6 classic + 6 premium, priced tiers) are kept in
     * the database and only deactivated, not deleted, so existing ad_payments /
     * invoices referencing them via payment_package_id stay intact.
     */
    public function up(): void
    {
        DB::table('payment_packages')->update(['is_active' => false]);

        $now = Carbon::now();

        DB::table('payment_packages')->insert([
            [
                'name' => 'Classic',
                'type' => 'classic',
                'duration_days' => 0,
                'price' => 0.00,
                'is_featured' => false,
                'is_top_ad' => false,
                'description' => 'Základné zobrazenie inzerátu zadarmo, bez časového limitu',
                'features' => json_encode(['Základné zobrazenie', 'Štandardná pozícia', 'Bez časového limitu']),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Premium 10 dní',
                'type' => 'premium',
                'duration_days' => 10,
                'price' => 10.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Topované zobrazenie inzerátu na 10 dní',
                'features' => json_encode(['Topované zobrazenie', 'Zvýraznenie', 'Priorita vo vyhľadávaní']),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Premium 30 dní',
                'type' => 'premium',
                'duration_days' => 30,
                'price' => 20.00,
                'is_featured' => true,
                'is_top_ad' => true,
                'description' => 'Topované zobrazenie inzerátu na 30 dní',
                'features' => json_encode(['Topované zobrazenie', 'Zvýraznenie', 'Priorita vo vyhľadávaní']),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reactivate the old tiered packages and deactivate the new ones.
     */
    public function down(): void
    {
        DB::table('payment_packages')
            ->where('name', 'Classic')
            ->where('duration_days', 0)
            ->delete();

        DB::table('payment_packages')
            ->whereIn('name', ['Premium 10 dní', 'Premium 30 dní'])
            ->delete();

        DB::table('payment_packages')->update(['is_active' => true]);
    }
};
