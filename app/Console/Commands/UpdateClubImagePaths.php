<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EroticClub;

class UpdateClubImagePaths extends Command
{
    protected $signature = 'clubs:update-image-paths';
    protected $description = 'Update erotic clubs image paths from WordPress URLs to local storage paths';

    public function handle()
    {
        $this->info('🔄 Aktualizujem cesty k obrázkom erotických klubov...');
        $this->newLine();
        
        $clubs = EroticClub::all();
        $updated = 0;
        
        foreach ($clubs as $club) {
            $clubUpdated = false;
            
            // Aktualizácia loga - nastavím cestu ak je logo_path NULL alebo WordPress URL
            if (!$club->logo_path || str_contains($club->logo_path, 'wp-content') || str_starts_with($club->logo_path, 'http')) {
                $logoPath = 'storage/clubs/logos/' . $club->slug . '.jpg';
                if (file_exists(public_path($logoPath))) {
                    $club->logo_path = $logoPath;
                    $clubUpdated = true;
                    $this->line("✅ Logo nastavené: {$club->name} -> {$logoPath}");
                } else {
                    $this->line("⚠️  Logo nenájdené pre: {$club->name} ({$logoPath})");
                }
            }
            
            // Pre hlavné obrázky - ponechám WordPress URL, model to vyrieši cez fallback
            if ($club->image_path && (str_contains($club->image_path, 'wp-content') || str_starts_with($club->image_path, 'http'))) {
                $this->line("⏳ Hlavný obrázok bude riešený cez fallback: {$club->name}");
            }
            
            if ($clubUpdated) {
                $club->save();
                $updated++;
            }
        }
        
        $this->newLine();
        $this->info("✨ Aktualizované: {$updated} klubov");
        
        // Test po aktualizácii
        $this->newLine();
        $this->info('🧪 Testujem po aktualizácii...');
        
        $testClubs = EroticClub::take(3)->get();
        foreach ($testClubs as $club) {
            $logoUrl = $club->logo_url;
            $logoExists = $logoUrl ? file_exists(public_path(str_replace(url('/'), '', $logoUrl))) : false;
            $this->line("   {$club->name}: Logo " . ($logoExists ? '✅' : '❌'));
        }
        
        return 0;
    }
} 