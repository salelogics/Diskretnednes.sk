<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CheckImageFiles extends Command
{
    protected $signature = 'check:image-files {--fix : Automatically fix found issues}';
    protected $description = 'Check and fix image file issues on hosting';

    public function handle()
    {
        $fix = $this->option('fix');
        $verbose = $this->option('verbose'); // Použijem built-in verbose
        
        $this->info('🔍 Checking image files on hosting...');
        
        // 1. Check directory structure
        $this->checkDirectoryStructure($fix);
        
        // 2. Check specific problematic files
        $this->checkProblematicFiles($fix, $verbose);
        
        // 3. Check database vs filesystem consistency
        $this->checkDatabaseConsistency($fix, $verbose);
        
        // 4. Check permissions
        $this->checkPermissions($fix);
        
        // 5. Create missing symbolic links
        $this->createSymbolicLinks($fix);
        
        $this->info('✅ Image file check completed!');
    }

    private function checkDirectoryStructure($fix)
    {
        $this->info('📁 Checking directory structure...');
        
        $directories = [
            'public/storage',
            'public/storage/ads',
            'public/storage/ads/verification',
            'public/storage/ads/gallery'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = base_path($dir);
            
            if (!File::exists($fullPath)) {
                $this->error("❌ Missing directory: $dir");
                
                if ($fix) {
                    File::makeDirectory($fullPath, 0755, true);
                    $this->info("✅ Created directory: $dir");
                }
            } else {
                $this->line("✅ Directory exists: $dir");
            }
        }
    }

    private function checkProblematicFiles($fix, $verbose)
    {
        $this->info('🔍 Checking specific problematic files...');
        
        $problematicFiles = [
            'verification_50758.jpg',
            'verification_50773.jpg',
            'verification_50841.jpg',
            'verification_50790.jpg',
            'verification_50671.jpg'
        ];
        
        foreach ($problematicFiles as $filename) {
            $this->line("Checking: $filename");
            
            // Check all possible locations
            $locations = [
                "public/storage/ads/$filename",
                "public/storage/ads/verification/$filename",
                "storage/app/public/ads/$filename",
                "storage/app/public/ads/verification/$filename"
            ];
            
            $found = false;
            $foundLocation = null;
            
            foreach ($locations as $location) {
                $fullPath = base_path($location);
                if (File::exists($fullPath)) {
                    $found = true;
                    $foundLocation = $location;
                    $fileSize = File::size($fullPath);
                    $permissions = substr(sprintf('%o', fileperms($fullPath)), -4);
                    
                    if ($verbose) {
                        $this->line("  ✅ Found at: $location (Size: $fileSize bytes, Permissions: $permissions)");
                    }
                    break;
                }
            }
            
            if (!$found) {
                $this->error("  ❌ File not found in any location: $filename");
                
                if ($fix) {
                    // Try to find the file in the root ads directory and move it
                    $rootPath = base_path("public/storage/ads/$filename");
                    if (File::exists($rootPath)) {
                        $targetPath = base_path("public/storage/ads/verification/$filename");
                        File::move($rootPath, $targetPath);
                        $this->info("  ✅ Moved to verification directory: $filename");
                    }
                }
            } else {
                // Check if file is in the correct location
                $correctPath = "public/storage/ads/verification/$filename";
                if ($foundLocation !== $correctPath) {
                    $this->warn("  ⚠️ File found but not in correct location: $foundLocation");
                    
                    if ($fix) {
                        $sourcePath = base_path($foundLocation);
                        $targetPath = base_path($correctPath);
                        
                        if (!File::exists($targetPath)) {
                            File::copy($sourcePath, $targetPath);
                            $this->info("  ✅ Copied to correct location: $filename");
                        }
                    }
                }
            }
        }
    }

    private function checkDatabaseConsistency($fix, $verbose)
    {
        $this->info('🔍 Checking database consistency...');
        
        // Check ads with verification photos
        $adsWithVerification = Ad::whereNotNull('verification_photo')->take(10)->get();
        
        foreach ($adsWithVerification as $ad) {
            $path = $ad->verification_photo;
            
            if ($verbose) {
                $this->line("AD #{$ad->id}: $path");
            }
            
            // Check if file exists
            $exists = $this->checkIfImageExists($path);
            
            if (!$exists) {
                $this->error("  ❌ File not found for AD #{$ad->id}: $path");
                
                if ($fix) {
                    // Try to find the file with different path formats
                    $filename = basename($path);
                    $possiblePaths = [
                        "public/storage/ads/$filename",
                        "public/storage/ads/verification/$filename",
                        "storage/app/public/ads/$filename",
                        "storage/app/public/ads/verification/$filename"
                    ];
                    
                    foreach ($possiblePaths as $possiblePath) {
                        if (File::exists(base_path($possiblePath))) {
                            // Update database with correct path
                            $normalizedPath = $this->normalizeImagePath($possiblePath);
                            $ad->update(['verification_photo' => $normalizedPath]);
                            $this->info("  ✅ Updated AD #{$ad->id} path to: $normalizedPath");
                            break;
                        }
                    }
                }
            } else {
                if ($verbose) {
                    $this->line("  ✅ File exists");
                }
            }
        }
    }

    private function checkIfImageExists($path)
    {
        if (empty($path)) {
            return false;
        }
        
        // Try different path formats
        $possiblePaths = [
            public_path('storage/ads/' . basename($path)),
            public_path('storage/ads/verification/' . basename($path)),
            public_path('storage/' . $path),
            public_path($path)
        ];
        
        foreach ($possiblePaths as $fullPath) {
            if (File::exists($fullPath)) {
                return true;
            }
        }
        
        return false;
    }

    private function normalizeImagePath($path)
    {
        $filename = basename($path);
        
        // For verification images, return just the filename
        if (str_contains($filename, 'verification_')) {
            return $filename;
        }
        
        // For gallery images, return just the filename
        if (str_contains($filename, 'gallery_')) {
            return $filename;
        }
        
        return $filename;
    }

    private function checkPermissions($fix)
    {
        $this->info('🔒 Checking file permissions...');
        
        $directories = [
            'public/storage',
            'public/storage/ads',
            'public/storage/ads/verification',
            'public/storage/ads/gallery'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = base_path($dir);
            
            if (File::exists($fullPath)) {
                $permissions = substr(sprintf('%o', fileperms($fullPath)), -4);
                
                if ($permissions !== '0755') {
                    $this->warn("⚠️ Incorrect permissions for $dir: $permissions (should be 0755)");
                    
                    if ($fix) {
                        chmod($fullPath, 0755);
                        $this->info("✅ Fixed permissions for: $dir");
                    }
                } else {
                    $this->line("✅ Correct permissions for: $dir");
                }
            }
        }
    }

    private function createSymbolicLinks($fix)
    {
        $this->info('🔗 Checking symbolic links...');
        
        // Check main storage symbolic link
        $publicStorage = public_path('storage');
        $targetStorage = storage_path('app/public');
        
        if (!File::exists($publicStorage)) {
            $this->error('❌ Main storage symbolic link missing');
            
            if ($fix) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $command = "mklink /J \"$publicStorage\" \"$targetStorage\"";
                    $output = shell_exec($command);
                    $this->info('✅ Created Windows junction for storage');
                } else {
                    symlink($targetStorage, $publicStorage);
                    $this->info('✅ Created symbolic link for storage');
                }
            }
        } else {
            $this->line('✅ Main storage symbolic link exists');
        }
        
        // Check if it's actually a link and working
        if (File::exists($publicStorage) && !is_link($publicStorage) && !is_dir($publicStorage)) {
            $this->error('❌ Storage path exists but is not a proper link/directory');
            
            if ($fix) {
                // Remove the broken link and recreate
                File::delete($publicStorage);
                
                if (PHP_OS_FAMILY === 'Windows') {
                    $command = "mklink /J \"$publicStorage\" \"$targetStorage\"";
                    shell_exec($command);
                } else {
                    symlink($targetStorage, $publicStorage);
                }
                
                $this->info('✅ Recreated storage symbolic link');
            }
        }
    }
} 