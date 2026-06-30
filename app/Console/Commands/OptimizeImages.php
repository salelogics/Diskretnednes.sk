<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Ad;

class OptimizeImages extends Command
{
    protected $signature = 'optimize:images {--format=webp : Convert to WebP format} {--quality=85 : Image quality (1-100)} {--dry-run : Show what would be done}';
    protected $description = 'Optimize images for better performance - resize, compress, and convert to WebP';

    public function handle()
    {
        $format = $this->option('format');
        $quality = (int) $this->option('quality');
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN - no actual changes will be made');
        }
        
        $this->info('🖼️  Starting image optimization...');
        
        $this->optimizeVerificationImages($format, $quality, $dryRun);
        $this->optimizeGalleryImages($format, $quality, $dryRun);
        
        $this->info('✅ Image optimization completed!');
        return 0;
    }
    
    private function optimizeVerificationImages($format, $quality, $dryRun)
    {
        $this->info('📸 Optimizing verification images...');
        
        $verificationPath = storage_path('app/public/ads/verification/');
        
        if (!File::exists($verificationPath)) {
            $this->warn('Verification images directory not found');
            return;
        }
        
        $files = File::files($verificationPath);
        $progressBar = $this->output->createProgressBar(count($files));
        
        foreach ($files as $file) {
            $this->optimizeImage($file, $format, $quality, $dryRun);
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
    }
    
    private function optimizeGalleryImages($format, $quality, $dryRun)
    {
        $this->info('🖼️  Optimizing gallery images...');
        
        $galleryPath = storage_path('app/public/ads/gallery/');
        
        if (!File::exists($galleryPath)) {
            $this->warn('Gallery images directory not found');
            return;
        }
        
        $files = File::files($galleryPath);
        $progressBar = $this->output->createProgressBar(count($files));
        
        foreach ($files as $file) {
            $this->optimizeImage($file, $format, $quality, $dryRun);
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
    }
    
    private function optimizeImage($file, $format, $quality, $dryRun)
    {
        $originalPath = $file->getRealPath();
        $originalSize = $file->getSize();
        $filename = $file->getFilename();
        
        if ($dryRun) {
            $this->line("Would optimize: {$filename} ({$this->formatBytes($originalSize)})");
            return;
        }
        
        try {
            // Load image
            $image = Image::make($originalPath);
            
            // Get original dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Resize if too large (max 1200px width for verification, 800px for gallery)
            $maxWidth = strpos($originalPath, 'verification') !== false ? 1200 : 800;
            
            if ($width > $maxWidth) {
                $image->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Convert to WebP if requested
            if ($format === 'webp') {
                $newPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $originalPath);
                $image->save($newPath, $quality);
                
                // Remove original if different
                if ($newPath !== $originalPath) {
                    File::delete($originalPath);
                }
            } else {
                // Just optimize quality
                $image->save($originalPath, $quality);
            }
            
            $newSize = File::size($format === 'webp' ? str_replace(['.jpg', '.jpeg', '.png'], '.webp', $originalPath) : $originalPath);
            $savings = round((($originalSize - $newSize) / $originalSize) * 100, 1);
            
            $this->line("✅ {$filename}: {$this->formatBytes($originalSize)} → {$this->formatBytes($newSize)} ({$savings}% saved)");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to optimize {$filename}: " . $e->getMessage());
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 