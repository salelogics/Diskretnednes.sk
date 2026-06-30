<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use App\Models\AdPayment;

class CheckAdFeatures extends Command
{
    protected $signature = 'test:check-ad-features {ad_id?}';
    protected $description = 'Skontroluje featured a top_ad hodnoty inzerátu';

    public function handle()
    {
        $adId = $this->argument('ad_id');
        
        if ($adId) {
            $ads = [Ad::find($adId)];
            if (!$ads[0]) {
                $this->error("Inzerát s ID {$adId} sa nenašiel!");
                return 1;
            }
        } else {
            // Zobraz posledných 5 aktivovaných inzerátov
            $ads = Ad::active()
                ->where('subscription_status', 'active')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        $this->info("=== KONTROLA FEATURED & TOP_AD ===\n");
        
        foreach ($ads as $ad) {
            $this->line("📋 INZERÁT ID: {$ad->id}");
            $this->line("   Telefón: {$ad->phone}");
            $this->line("   Status: {$ad->status}");
            $this->line("   Subscription: {$ad->subscription_status}");
            $this->line("   Featured: " . ($ad->featured ? '✅ YES' : '❌ NO'));
            $this->line("   Top Ad: " . ($ad->top_ad ? '✅ YES' : '❌ NO'));
            $this->line("   Badge sa zobrazí: " . ($ad->top_ad ? '🏆 ÁNO - "Topované"' : '⭐ NIE - žiadny badge'));
            $this->line("   Expires: " . ($ad->subscription_expires_at ? $ad->subscription_expires_at->format('Y-m-d H:i:s') : 'Nenastavené'));
            
            // Najdi posledné payment pre tento inzerát
            $payment = AdPayment::where('ad_id', $ad->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($payment) {
                $packageType = $payment->paymentPackage ? $payment->paymentPackage->type : 'Neznámy';
                $this->line("   💰 Platba: {$payment->payment_method} | Balík: {$packageType}");
                $this->line("   💰 Payment featured: " . ($payment->is_featured ? '✅ YES' : '❌ NO'));
                $this->line("   💰 Payment top_ad: " . ($payment->is_top_ad ? '✅ YES' : '❌ NO'));
            } else {
                $this->line("   💰 Žiadna platba nenájdená");
            }
            
            $this->newLine();
        }
        
        // Kontrola carousel inzerátov
        $topAds = Ad::active()
            ->where('subscription_status', 'active')
            ->where(function($q) {
                $q->where('top_ad', true)
                  ->orWhere('featured', true);
            })
            ->get(['id', 'phone', 'featured', 'top_ad']);
            
        $this->info("🎠 CAROUSEL INZERÁTY (top_ad=true ALEBO featured=true):");
        $this->line("   Počet inzerátov v carousel: {$topAds->count()}");
        
        foreach ($topAds as $topAd) {
            $this->line("   ID {$topAd->id} ({$topAd->phone}) | featured=" . ($topAd->featured ? '✅' : '❌') . " | top_ad=" . ($topAd->top_ad ? '✅' : '❌'));
        }
        
        return 0;
    }
} 