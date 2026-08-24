<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\PaymentPackage;

return new class extends Migration
{
    /**
     * Fixes the pink placeholder rectangles on the homepage.
     *
     * The earlier "top up visible ads to 50" migrations activated the oldest
     * draft/pending ads without checking whether they actually have a usable
     * photo. Draft ads are exactly the ones whose owner never finished the
     * wizard, so many of them have no verification photo and an empty
     * gallery - partials/ad-grid.blade.php then falls through to its
     * `bg-gradient-to-br from-pink-400 to-pink-600` placeholder, which is
     * what shows up as a blank pink card.
     *
     * This migration:
     *  1. Reverts every ad activated by that bulk top-up (identified by the
     *     synthetic AdPayment's metadata.bulk_activation flag) that has no
     *     displayable photo, back to its recorded previous status.
     *  2. Tops the count back up to 50, this time only from candidates that
     *     really do have a photo.
     *
     * Ads that were already active before the bulk top-up are never touched -
     * only ones carrying our own bulk_activation marker are considered.
     */
    public function up(): void
    {
        $target = 50;

        // --- 1. Revert photo-less ads we activated in bulk -------------------
        $bulkPayments = AdPayment::where('payment_method', 'free')
            ->whereNotNull('metadata')
            ->get()
            ->filter(fn ($payment) => ($payment->metadata['bulk_activation'] ?? false) === true);

        foreach ($bulkPayments as $payment) {
            $ad = $payment->ad;

            if (!$ad || $this->hasDisplayablePhoto($ad)) {
                continue;
            }

            $ad->update([
                'status' => $payment->metadata['previous_status'] ?? 'draft',
                'subscription_status' => 'inactive',
                'subscription_expires_at' => null,
                'featured' => false,
                'top_ad' => false,
            ]);

            // payment_method 'free' never generates an invoice, so there is no
            // invoice row pointing at this payment - cancelling is enough and
            // keeps the audit trail instead of deleting history.
            $payment->update(['status' => 'cancelled']);
        }

        // --- 2. Top back up to 50, photos required ---------------------------
        $currentlyVisible = Ad::active()->withActiveSubscription()->count();
        $needed = $target - $currentlyVisible;

        if ($needed <= 0) {
            return;
        }

        $classicPackage = PaymentPackage::where('type', 'classic')->where('is_active', true)->first();

        if (!$classicPackage) {
            return;
        }

        // Cheap pre-filter in SQL (a photo column must at least be non-empty),
        // then the authoritative per-ad check below, which also verifies the
        // referenced file actually exists on disk.
        $candidates = Ad::whereIn('status', ['draft', 'pending'])
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

    /**
     * Mirrors exactly what partials/ad-grid.blade.php checks before falling
     * back to the pink placeholder, including the accessors' on-disk file
     * existence check - so "has a photo" here means "renders a real image".
     */
    private function hasDisplayablePhoto(Ad $ad): bool
    {
        return (bool) $ad->verification_image_url || count($ad->gallery_image_urls) > 0;
    }

    /**
     * Intentionally irreversible - reverting would mean re-activating ads we
     * deliberately hid for having no photo.
     */
    public function down(): void
    {
    }
};
