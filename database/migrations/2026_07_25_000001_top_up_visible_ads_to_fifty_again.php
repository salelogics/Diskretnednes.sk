<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\PaymentPackage;

return new class extends Migration
{
    /**
     * Migrations only ever run once, so the earlier top-up
     * (2026_07_17_000001) couldn't re-run once new ads shifted the count -
     * and it was written before the deploy pipeline actually executed
     * `migrate` on every push, so on this environment it may not have run
     * at all yet either way. Same idempotent top-up, re-checked from
     * scratch: only activates more draft/pending ads if still below 50
     * visible (status=active AND active subscription).
     */
    public function up(): void
    {
        $target = 50;

        $currentlyVisible = Ad::active()->withActiveSubscription()->count();
        $needed = $target - $currentlyVisible;

        if ($needed <= 0) {
            return;
        }

        $classicPackage = PaymentPackage::where('type', 'classic')->where('is_active', true)->first();

        if (!$classicPackage) {
            return;
        }

        $candidates = Ad::whereIn('status', ['draft', 'pending'])
            ->orderBy('created_at', 'asc')
            ->limit($needed)
            ->get();

        foreach ($candidates as $ad) {
            $payment = AdPayment::create([
                'user_id' => $ad->user_id,
                'ad_id' => $ad->id,
                'payment_package_id' => $classicPackage->id,
                'payment_id' => str_pad((string) mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                'amount' => 0,
                'currency' => 'EUR',
                'payment_method' => 'free',
                'status' => 'pending',
                'duration_days' => $classicPackage->duration_days,
                'is_featured' => $classicPackage->is_featured,
                'is_top_ad' => $classicPackage->is_top_ad,
                'metadata' => [
                    'package_name' => $classicPackage->name,
                    'package_type' => $classicPackage->type,
                    'bulk_activation' => true,
                    'previous_status' => $ad->status,
                ],
            ]);

            $payment->markAsCompleted();
        }
    }

    /**
     * Intentionally irreversible - we don't record which ads this migration
     * touched vs. were already active, so there's no safe way to revert
     * only the ones it activated.
     */
    public function down(): void
    {
    }
};
