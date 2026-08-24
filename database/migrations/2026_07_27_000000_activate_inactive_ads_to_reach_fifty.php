<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\PaymentPackage;

return new class extends Migration
{
    /**
     * Further top-up towards 50 visible ads. The previous top-ups
     * (2026_07_17_000001, 2026_07_25_000001) only drew from 'draft'/'pending'
     * and deliberately left 'inactive' alone. Per explicit instruction, this
     * one also draws from 'inactive' - those were deactivated in bulk
     * (not individually moderated the way 'rejected' ads were), so
     * reactivating them here is intentional, not overriding a rejection.
     *
     * 'rejected' remains untouched - that status is an explicit moderation
     * decision, unlike a generic 'inactive' toggle.
     *
     * Still requires a displayable photo (same check as
     * 2026_07_26_000000_revert_photoless_bulk_activated_ads.php), so this
     * doesn't reintroduce the earlier blank-pink-card bug.
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

        $candidates = Ad::whereIn('status', ['draft', 'pending', 'inactive'])
            ->where(function ($query) {
                $query->whereNotNull('verification_photo')
                    ->where('verification_photo', '!=', '')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('gallery_photos')
                            ->whereNotIn('gallery_photos', ['[]', '""', 'null']);
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $activated = 0;

        foreach ($candidates as $ad) {
            if ($activated >= $needed) {
                break;
            }

            if (!$this->hasDisplayablePhoto($ad)) {
                continue;
            }

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
            $activated++;
        }
    }

    private function hasDisplayablePhoto(Ad $ad): bool
    {
        return (bool) $ad->verification_image_url || count($ad->gallery_image_urls) > 0;
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
