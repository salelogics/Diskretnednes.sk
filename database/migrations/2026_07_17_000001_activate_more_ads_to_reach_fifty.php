<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\PaymentPackage;

return new class extends Migration
{
    /**
     * Tops up the number of publicly visible ads (status=active AND an
     * active subscription - the exact criteria the homepage query uses) to
     * at least 50, by activating existing 'draft' and 'pending' ads with
     * the free Classic package.
     *
     * Only 'draft' (never finished the former paid checkout - Classic is
     * free now, so nothing should be blocking them anymore) and 'pending'
     * (submitted, awaiting approval) ads are touched, oldest first.
     * 'inactive' (explicitly paused, by the owner or an admin) and
     * 'rejected' (explicitly moderated out) ads are deliberately left
     * alone - reactivating those would override a deliberate decision this
     * migration has no way to safely second-guess.
     *
     * Each activated ad gets a real AdPayment record (payment_method
     * 'free', same as the self-service free Classic flow) so admin payment
     * history stays consistent, rather than editing the Ad row directly.
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
