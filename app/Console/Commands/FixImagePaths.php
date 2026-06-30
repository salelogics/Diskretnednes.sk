<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FixImagePaths extends Command
{
    protected $signature = 'fix:image-paths {--dry-run : Show what would be done without actually doing it}';
    protected $description = 'Fix image paths for ads by moving files to correct directories and updating database';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting image path migration...');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }
        
        $this->migrateVerificationImages($isDryRun);
        $this->migrateGalleryImages($isDryRun);
        $this->createSymbolicLinks($isDryRun);
        
        $this->info('Image path migration completed!');
    }

    private function migrateVerificationImages($isDryRun)
    {
        $this->info('Migrating verification images...');
        
        $sourceDir = public_path('storage/ads');
        $targetDir = public_path('storage/ads/verification');
        
        // Nájdi všetky verification súbory v koreňovom priečinku
        $files = File::glob($sourceDir . '/verification_*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        $this->info('Found ' . count($files) . ' verification files to migrate');
        
        $migratedCount = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            $targetPath = $targetDir . '/' . $filename;
            
            if (File::exists($targetPath)) {
                $this->warn("Target file already exists: $filename");
                continue;
            }
            
            $this->line("Moving: $filename");
            
            if (!$isDryRun) {
                if (File::move($file, $targetPath)) {
                    $migratedCount++;
                } else {
                    $this->error("Failed to move: $filename");
                }
            } else {
                $migratedCount++;
            }
        }
        
        $this->info("Migrated $migratedCount verification images");
        
        // Aktualizuj databázu
        $this->updateVerificationPaths($isDryRun);
    }

    private function migrateGalleryImages($isDryRun)
    {
        $this->info('Migrating gallery images...');
        
        $sourceDir = public_path('storage/ads');
        $targetDir = public_path('storage/ads/gallery');
        
        // Nájdi všetky gallery súbory v koreňovom priečinku
        $files = File::glob($sourceDir . '/gallery_*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        $this->info('Found ' . count($files) . ' gallery files to migrate');
        
        $migratedCount = 0;
        
        foreach ($files as $file) {
            $filename = basename($file);
            $targetPath = $targetDir . '/' . $filename;
            
            if (File::exists($targetPath)) {
                $this->warn("Target file already exists: $filename");
                continue;
            }
            
            $this->line("Moving: $filename");
            
            if (!$isDryRun) {
                if (File::move($file, $targetPath)) {
                    $migratedCount++;
                } else {
                    $this->error("Failed to move: $filename");
                }
            } else {
                $migratedCount++;
            }
        }
        
        $this->info("Migrated $migratedCount gallery images");
        
        // Aktualizuj databázu
        $this->updateGalleryPaths($isDryRun);
    }

    private function updateVerificationPaths($isDryRun)
    {
        $this->info('Updating verification paths in database...');
        
        $ads = Ad::whereNotNull('verification_photo')->get();
        $updatedCount = 0;
        
        foreach ($ads as $ad) {
            $originalPath = $ad->verification_photo;
            $newPath = $this->normalizeVerificationPath($originalPath);
            
            if ($originalPath !== $newPath) {
                $this->line("Updating AD #{$ad->id}: $originalPath -> $newPath");
                
                if (!$isDryRun) {
                    $ad->update(['verification_photo' => $newPath]);
                }
                $updatedCount++;
            }
        }
        
        $this->info("Updated $updatedCount verification paths in database");
    }

    private function updateGalleryPaths($isDryRun)
    {
        $this->info('Updating gallery paths in database...');
        
        $ads = Ad::whereNotNull('gallery_photos')->get();
        $updatedCount = 0;
        
        foreach ($ads as $ad) {
            if (!is_array($ad->gallery_photos)) {
                continue;
            }
            
            $originalPhotos = $ad->gallery_photos;
            $newPhotos = [];
            $hasChanges = false;
            
            foreach ($originalPhotos as $photo) {
                $newPath = $this->normalizeGalleryPath($photo);
                $newPhotos[] = $newPath;
                
                if ($photo !== $newPath) {
                    $hasChanges = true;
                }
            }
            
            if ($hasChanges) {
                $this->line("Updating AD #{$ad->id} gallery paths");
                
                if (!$isDryRun) {
                    $ad->update(['gallery_photos' => $newPhotos]);
                }
                $updatedCount++;
            }
        }
        
        $this->info("Updated $updatedCount gallery paths in database");
    }

    private function normalizeVerificationPath($path)
    {
        if (empty($path)) {
            return $path;
        }
        
        // Ak je to externá URL, nechaj ju ako je
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // Rôzne formáty na normalizáciu:
        
        // 1. Len názov súboru (verification_XXXXX.jpg) -> nechaj ako je
        if (preg_match('/^verification_\d+\.(jpg|jpeg|png|gif)$/i', $path)) {
            return $path;
        }
        
        // 2. Starý formát: storage/ads/verification_XXXXX.jpg -> verification_XXXXX.jpg
        if (preg_match('/^storage\/ads\/verification_(\d+\.(jpg|jpeg|png|gif))$/i', $path, $matches)) {
            return 'verification_' . $matches[1];
        }
        
        // 3. Nový formát: storage/ads/verification/verification_XXXXX.jpg -> nechaj ako je
        if (str_starts_with($path, 'storage/ads/verification/')) {
            return $path;
        }
        
        // 4. Duplikátne cesty: storage/ads/ads/verification/ -> storage/ads/verification/
        if (str_contains($path, 'storage/ads/ads/verification/')) {
            return str_replace('storage/ads/ads/verification/', 'storage/ads/verification/', $path);
        }
        
        // 5. Ak je to len basename, pridaj správny prefix
        $basename = basename($path);
        if (preg_match('/^verification_\d+\.(jpg|jpeg|png|gif)$/i', $basename)) {
            return $basename;
        }
        
        // 6. Fallback - nechaj ako je
        return $path;
    }

    private function normalizeGalleryPath($path)
    {
        if (empty($path)) {
            return $path;
        }
        
        // Ak je to externá URL, nechaj ju ako je
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // Rôzne formáty na normalizáciu:
        
        // 1. Len názov súboru (gallery_XXXXX.jpg) -> nechaj ako je
        if (preg_match('/^gallery_\d+\.(jpg|jpeg|png|gif)$/i', $path)) {
            return $path;
        }
        
        // 2. Starý formát: storage/ads/gallery_XXXXX.jpg -> gallery_XXXXX.jpg
        if (preg_match('/^storage\/ads\/gallery_(\d+\.(jpg|jpeg|png|gif))$/i', $path, $matches)) {
            return 'gallery_' . $matches[1];
        }
        
        // 3. Nový formát: storage/ads/gallery/gallery_XXXXX.jpg -> nechaj ako je
        if (str_starts_with($path, 'storage/ads/gallery/')) {
            return $path;
        }
        
        // 4. Duplikátne cesty: storage/ads/ads/gallery/ -> storage/ads/gallery/
        if (str_contains($path, 'storage/ads/ads/gallery/')) {
            return str_replace('storage/ads/ads/gallery/', 'storage/ads/gallery/', $path);
        }
        
        // 5. Ak je to len basename, pridaj správny prefix
        $basename = basename($path);
        if (preg_match('/^gallery_\d+\.(jpg|jpeg|png|gif)$/i', $basename)) {
            return $basename;
        }
        
        // 6. Fallback - nechaj ako je
        return $path;
    }

    private function createSymbolicLinks($isDryRun)
    {
        $this->info('Creating symbolic links for backward compatibility...');
        
        $sourceDir = public_path('storage/ads');
        $verificationDir = public_path('storage/ads/verification');
        $galleryDir = public_path('storage/ads/gallery');
        
        // Vytvor symbolické odkazy pre starý formát
        $verificationFiles = File::glob($verificationDir . '/verification_*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $galleryFiles = File::glob($galleryDir . '/gallery_*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        $linkCount = 0;
        
        foreach ($verificationFiles as $file) {
            $filename = basename($file);
            $linkPath = $sourceDir . '/' . $filename;
            
            if (!File::exists($linkPath)) {
                $this->line("Creating link: $filename");
                
                if (!$isDryRun) {
                    // Na Windows vytvor junction, na Unix symlink
                    if (PHP_OS_FAMILY === 'Windows') {
                        $result = shell_exec("mklink /J \"$linkPath\" \"$file\"");
                    } else {
                        $result = symlink($file, $linkPath);
                    }
                    
                    if ($result) {
                        $linkCount++;
                    }
                } else {
                    $linkCount++;
                }
            }
        }
        
        foreach ($galleryFiles as $file) {
            $filename = basename($file);
            $linkPath = $sourceDir . '/' . $filename;
            
            if (!File::exists($linkPath)) {
                $this->line("Creating link: $filename");
                
                if (!$isDryRun) {
                    // Na Windows vytvor junction, na Unix symlink
                    if (PHP_OS_FAMILY === 'Windows') {
                        $result = shell_exec("mklink /J \"$linkPath\" \"$file\"");
                    } else {
                        $result = symlink($file, $linkPath);
                    }
                    
                    if ($result) {
                        $linkCount++;
                    }
                } else {
                    $linkCount++;
                }
            }
        }
        
        $this->info("Created $linkCount symbolic links");
    }
} 