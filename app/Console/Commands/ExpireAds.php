<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireAds extends Command
{
    protected $signature = 'ads:expire {--dry-run : Len zobraziť čo by sa zmenilo bez vykonania zmien} {--force : Vykonať bez potvrdenia}';
    protected $description = 'Deaktivuje inzeráty s expirovaným predplatným';

    public function __construct(protected NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔍 Kontrola expirovaných inzerátov...');
        $this->line('Aktuálny čas: ' . now()->format('d.m.Y H:i:s'));
        $this->newLine();

        // Nájdeme expirované inzeráty
        $expiredAds = Ad::where('subscription_status', 'active')
            ->where('subscription_expires_at', '<', now())
            ->with('user')
            ->get();

        if ($expiredAds->isEmpty()) {
            $this->info('✅ Žiadne expirované inzeráty neboli nájdené.');
            return 0;
        }

        $this->warn("⚠️  Nájdených {$expiredAds->count()} expirovaných inzerátov:");
        $this->newLine();

        // Zobrazíme detaily expirovaných inzerátov
        $table = [];
        foreach ($expiredAds as $ad) {
            $expiredDays = now()->diffInDays($ad->subscription_expires_at);
            $table[] = [
                'ID' => $ad->id,
                'Názov' => \Str::limit($ad->nickname ?? 'Bez názvu', 30),
                'Používateľ' => $ad->user->name ?? 'Neznámy',
                'Expiroval' => $ad->subscription_expires_at->format('d.m.Y H:i'),
                'Pred dňami' => $expiredDays,
                'Status' => $ad->status,
                'Sub Status' => $ad->subscription_status
            ];
        }

        $this->table(
            ['ID', 'Názov', 'Používateľ', 'Expiroval', 'Pred dňami', 'Status', 'Sub Status'],
            $table
        );

        if ($dryRun) {
            $this->info('🔍 DRY RUN - Žiadne zmeny neboli vykonané.');
            $this->line('Spustite bez --dry-run na skutočné vykonanie zmien.');
            return 0;
        }

        // Potvrdenie od používateľa (ak nie je --force)
        if (!$force) {
            if (!$this->confirm("Skutočne chcete deaktivovať {$expiredAds->count()} expirovaných inzerátov?")) {
                $this->info('❌ Operácia zrušená používateľom.');
                return 0;
            }
        }

        $this->info('🔄 Deaktivujem expirované inzeráty...');
        $deactivatedCount = 0;
        $errorCount = 0;

        foreach ($expiredAds as $ad) {
            try {
                // Deaktivujeme inzerát
                $ad->update([
                    'subscription_status' => 'expired'
                ]);

                // Vytvoríme notifikáciu pre používateľa
                $this->notificationService->subscriptionExpired($ad);

                $this->line("✅ Deaktivovaný inzerát ID:{$ad->id} ({$ad->nickname})");
                $deactivatedCount++;

                Log::info('Ad subscription expired and deactivated', [
                    'ad_id' => $ad->id,
                    'user_id' => $ad->user_id,
                    'expired_at' => $ad->subscription_expires_at,
                    'deactivated_at' => now()
                ]);

            } catch (\Exception $e) {
                $this->error("❌ Chyba pri deaktivácii inzerátu ID:{$ad->id}: {$e->getMessage()}");
                $errorCount++;

                Log::error('Failed to deactivate expired ad', [
                    'ad_id' => $ad->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("✅ Úspešne dokončené!");
        $this->line("   Deaktivovaných: {$deactivatedCount}");
        if ($errorCount > 0) {
            $this->line("   Chýb: {$errorCount}");
        }

        // Štatistiky po zmene
        $this->newLine();
        $this->info('📊 Aktuálne štatistiky inzerátov:');
        
        $statusCounts = Ad::selectRaw('status, subscription_status, COUNT(*) as count')
            ->groupBy('status', 'subscription_status')
            ->orderBy('status')
            ->orderBy('subscription_status')
            ->get();

        $statsTable = [];
        foreach ($statusCounts as $stat) {
            $statsTable[] = [
                'Status' => $stat->status,
                'Subscription' => $stat->subscription_status,
                'Počet' => $stat->count
            ];
        }

        $this->table(['Status', 'Subscription', 'Počet'], $statsTable);

        return 0;
    }
} 