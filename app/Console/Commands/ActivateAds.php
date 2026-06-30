<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;

class ActivateAds extends Command
{
    protected $signature = 'ads:activate {--all : Aktivuj všetky inzeráty} {--status=draft : Status inzerátov na aktiváciu}';
    protected $description = 'Aktivácia inzerátov';

    public function handle()
    {
        $activateAll = $this->option('all');
        $status = $this->option('status');

        $this->info("Aktivácia inzerátov...");

        // Zobraz aktuálny stav
        $statusCounts = Ad::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $this->info("Aktuálny stav inzerátov:");
        foreach ($statusCounts as $statusCount) {
            $this->line("  {$statusCount->status}: {$statusCount->count}");
        }

        if ($activateAll) {
            // Aktivuj všetky inzeráty
            $updated = Ad::whereIn('status', ['draft', 'inactive'])
                ->update([
                    'status' => 'active',
                    'subscription_status' => 'active',
                    'subscription_expires_at' => now()->addDays(30)
                ]);
            
            $this->info("Aktivovaných {$updated} inzerátov (všetky draft a inactive)");
        } else {
            // Aktivuj len inzeráty s konkrétnym statusom
            $updated = Ad::where('status', $status)
                ->update([
                    'status' => 'active',
                    'subscription_status' => 'active', 
                    'subscription_expires_at' => now()->addDays(30)
                ]);
            
            $this->info("Aktivovaných {$updated} inzerátov so statusom '{$status}'");
        }

        // Zobraz nový stav
        $newStatusCounts = Ad::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $this->info("Nový stav inzerátov:");
        foreach ($newStatusCounts as $statusCount) {
            $this->line("  {$statusCount->status}: {$statusCount->count}");
        }

        // Zobraz subscription status
        $subscriptionCounts = Ad::selectRaw('subscription_status, COUNT(*) as count')
            ->groupBy('subscription_status')
            ->get();

        $this->info("Subscription status:");
        foreach ($subscriptionCounts as $subCount) {
            $this->line("  {$subCount->subscription_status}: {$subCount->count}");
        }

        return 0;
    }
} 