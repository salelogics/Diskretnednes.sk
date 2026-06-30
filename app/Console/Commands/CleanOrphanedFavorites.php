<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class CleanOrphanedFavorites extends Command
{
    protected $signature = 'favorites:clean-orphaned {--dry-run : Iba zobraziť čo by sa vymazalo}';
    
    protected $description = 'Vymaže obľúbené inzeráty ktoré sú spojené s neexistujúcimi session';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🧹 Hľadám orphaned favorites...');
        
        // Nájdi favorites s session_id ktoré nemajú existujúce session
        $orphanedQuery = Favorite::whereNotNull('session_id')
            ->whereNotExists(function($query) {  
                $query->select(DB::raw(1))
                      ->from('sessions')
                      ->whereRaw('sessions.id = favorites.session_id');
            });
            
        $orphanedCount = $orphanedQuery->count();
        
        if ($orphanedCount === 0) {
            $this->info('✅ Žiadne orphaned favorites nenájdené.');
            return 0;
        }
        
        $this->warn("⚠️  Nájdených {$orphanedCount} orphaned favorites.");
        
        if ($dryRun) {
            $this->line('🔍 DRY RUN - žiadne zmeny sa nevykonajú');
        } else {
            $deleted = $orphanedQuery->delete();
            $this->info("✅ Vymazané orphaned favorites: {$deleted}");
        }
        
        return 0;
    }
}