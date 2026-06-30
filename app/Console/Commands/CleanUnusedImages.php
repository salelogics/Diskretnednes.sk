<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanUnusedImages extends Command
{
    protected $signature = 'ads:clean-unused-images {--dry-run : Len ukáž čo by sa vymazalo}';
    protected $description = 'Vymaže nepoužívané obrázky, ktoré nepatria k žiadnym inzerátom';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Spúšťam čistenie nepoužívaných obrázkov...');
        
        if ($dryRun) {
            $this->warn('DRY RUN - žiadne súbory sa nevymažú');
        }

        // Získaj všetky WordPress ID z databázy
        $existingWpIds = Ad::whereNotNull('wp_id')->pluck('wp_id')->toArray();
        $this->info("Inzeráty v databáze majú WP ID: " . min($existingWpIds) . " - " . max($existingWpIds));
        
        // Skontroluj galériu
        $galleryPath = storage_path('app/public/ads/gallery');
        $verificationPath = storage_path('app/public/ads/verification');
        
        $deletedCount = 0;
        $keptCount = 0;
        $totalSize = 0;
        
        // Spracuj galériu
        if (File::exists($galleryPath)) {
            $galleryFiles = File::files($galleryPath);
            $this->info("Našiel som " . count($galleryFiles) . " súborov v galérii");
            
            foreach ($galleryFiles as $file) {
                $filename = $file->getFilename();
                
                if (preg_match('/gallery_(\d+)_/', $filename, $matches)) {
                    $wpId = (int)$matches[1];
                    
                    if (!in_array($wpId, $existingWpIds)) {
                        // Tento súbor nepatrí k žiadnemu inzerátu
                        $size = $file->getSize();
                        $totalSize += $size;
                        
                        if ($dryRun) {
                            $this->line("  -> Vymazal by sa: {$filename} (" . $this->formatBytes($size) . ")");
                        } else {
                            if (File::delete($file->getPathname())) {
                                $this->line("  -> Vymazané: {$filename} (" . $this->formatBytes($size) . ")");
                            } else {
                                $this->error("  -> Chyba pri mazaní: {$filename}");
                            }
                        }
                        $deletedCount++;
                    } else {
                        $keptCount++;
                    }
                } else {
                    $this->warn("Nerozpoznal som formát súboru: {$filename}");
                }
            }
        }
        
        // Spracuj verifikačné fotky
        if (File::exists($verificationPath)) {
            $verificationFiles = File::files($verificationPath);
            $this->info("Našiel som " . count($verificationFiles) . " verifikačných súborov");
            
            foreach ($verificationFiles as $file) {
                $filename = $file->getFilename();
                
                if (preg_match('/verification_(\d+)_/', $filename, $matches)) {
                    $wpId = (int)$matches[1];
                    
                    if (!in_array($wpId, $existingWpIds)) {
                        $size = $file->getSize();
                        $totalSize += $size;
                        
                        if ($dryRun) {
                            $this->line("  -> Vymazal by sa: {$filename} (" . $this->formatBytes($size) . ")");
                        } else {
                            if (File::delete($file->getPathname())) {
                                $this->line("  -> Vymazané: {$filename} (" . $this->formatBytes($size) . ")");
                            } else {
                                $this->error("  -> Chyba pri mazaní: {$filename}");
                            }
                        }
                        $deletedCount++;
                    } else {
                        $keptCount++;
                    }
                } else {
                    // Možno sú to súbory s iným formátom názvu, nechaj ich
                    $this->warn("Nerozpoznal som formát verifikačného súboru: {$filename} - nechávam");
                    $keptCount++;
                }
            }
        }

        $this->newLine();
        $this->info("=== VÝSLEDKY ČISTENIA ===");
        $this->info("Súbory na vymazanie: {$deletedCount}");
        $this->info("Ponechané súbory: {$keptCount}");
        $this->info("Celková veľkosť na vymazanie: " . $this->formatBytes($totalSize));

        if ($dryRun) {
            $this->warn("DRY RUN - spustite bez --dry-run pre skutočné vymazanie");
        } else {
            $this->info("Čistenie dokončené!");
        }

        return 0;
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 