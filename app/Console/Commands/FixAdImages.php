<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FixAdImages extends Command
{
    protected $signature = 'ads:fix-images {--dry-run : Len ukáž čo by sa opravilo}';
    protected $description = 'Opraví priradenie obrázkov k inzerátom na základe už stiahnutých súborov';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Spúšťam opravu priradenia obrázkov k inzerátom...');
        
        if ($dryRun) {
            $this->warn('DRY RUN - žiadne zmeny sa neuložia');
        }

        // Získaj všetky súbory z galérie
        $galleryPath = storage_path('app/public/ads/gallery');
        $verificationPath = storage_path('app/public/ads/verification');
        
        if (!File::exists($galleryPath)) {
            $this->error("Priečinok galérie neexistuje: {$galleryPath}");
            return 1;
        }

        $galleryFiles = File::files($galleryPath);
        $verificationFiles = File::exists($verificationPath) ? File::files($verificationPath) : [];
        
        $this->info("Našiel som " . count($galleryFiles) . " súborov v galérii");
        $this->info("Našiel som " . count($verificationFiles) . " verifikačných súborov");

        $fixed = 0;
        $errors = 0;

        // Spracovanie súborov galérie
        foreach ($galleryFiles as $file) {
            $filename = $file->getFilename();
            
            // Parsuj názov súboru: gallery_{wp_id}_{attachment_id}_{index}.jpg
            if (preg_match('/gallery_(\d+)_(\d+)_(\d+)\.(jpg|jpeg|png|gif)/', $filename, $matches)) {
                $wpId = $matches[1];
                $attachmentId = $matches[2];
                $index = $matches[3];
                $extension = $matches[4];
                
                $this->line("Spracovávam: {$filename} (WP ID: {$wpId}, Attachment: {$attachmentId}, Index: {$index})");
                
                // Nájdi inzerát podľa wp_id
                $ad = Ad::where('wp_id', $wpId)->first();
                
                if (!$ad) {
                    $this->warn("  -> Nenašiel som inzerát s wp_id: {$wpId}");
                    $errors++;
                    continue;
                }
                
                // Pridaj cestu do galérie
                                    $imagePath = "ads/gallery/{$filename}";
                $currentGallery = $ad->gallery_photos ?? [];
                
                if (!in_array($imagePath, $currentGallery)) {
                    $currentGallery[] = $imagePath;
                    
                    if (!$dryRun) {
                        $ad->update(['gallery_photos' => $currentGallery]);
                    }
                    
                    $this->info("  -> Pridané do inzerátu #{$ad->id} ({$ad->nickname})");
                    $fixed++;
                } else {
                    $this->line("  -> Už existuje v galérii inzerátu #{$ad->id}");
                }
            } else {
                $this->warn("Nerozpoznal som formát súboru: {$filename}");
            }
        }

        // Spracovanie verifikačných súborov
        foreach ($verificationFiles as $file) {
            $filename = $file->getFilename();
            
            // Parsuj názov súboru: verification_{wp_id}_{attachment_id}.jpg
            if (preg_match('/verification_(\d+)_(\d+)\.(jpg|jpeg|png|gif)/', $filename, $matches)) {
                $wpId = $matches[1];
                $attachmentId = $matches[2];
                $extension = $matches[3];
                
                $this->line("Spracovávam verifikačnú fotku: {$filename} (WP ID: {$wpId})");
                
                // Nájdi inzerát podľa wp_id
                $ad = Ad::where('wp_id', $wpId)->first();
                
                if (!$ad) {
                    $this->warn("  -> Nenašiel som inzerát s wp_id: {$wpId}");
                    $errors++;
                    continue;
                }
                
                // Nastav verifikačnú fotku
                                    $imagePath = "ads/verification/{$filename}";
                
                if ($ad->verification_photo !== $imagePath) {
                    if (!$dryRun) {
                        $ad->update(['verification_photo' => $imagePath]);
                    }
                    
                    $this->info("  -> Nastavené ako verifikačná fotka pre inzerát #{$ad->id} ({$ad->nickname})");
                    $fixed++;
                } else {
                    $this->line("  -> Už nastavené ako verifikačná fotka pre inzerát #{$ad->id}");
                }
            } else {
                $this->warn("Nerozpoznal som formát verifikačného súboru: {$filename}");
            }
        }

        $this->newLine();
        $this->info("=== VÝSLEDKY OPRAVY ===");
        $this->info("Opravené obrázky: {$fixed}");
        $this->info("Chyby: {$errors}");

        if ($dryRun) {
            $this->warn("DRY RUN - spustite bez --dry-run pre aplikovanie zmien");
        }

        return 0;
    }
} 