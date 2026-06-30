<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\PaymentPackage;
use App\Models\Ad;

class PostDeploy extends Command
{
    protected $signature = 'post:deploy {--fix : Automatically fix found issues}';
    protected $description = 'Post-deployment command that handles all necessary setup and fixes';

    public function handle()
    {
        $fix = $this->option('fix');
        
        $this->info('🚀 Starting post-deployment setup...');
        
        // 1. Skontroluj databázu
        $this->checkDatabase();
        
        // 2. Oprav image paths
        $this->fixImagePaths($fix);
        
        // 3. Skontroluj a oprav image súbory
        $this->checkImageFiles($fix);
        
        // 4. Zabezpeč symlinky
        $this->ensureSymlinks();
        
        // 5. Finálna diagnostika
        $this->finalCheck();
        
        $this->info('✅ Post-deployment setup completed!');
        return 0;
    }
    
    private function checkDatabase()
    {
        $this->info('📊 Checking database...');
        
        // Skontroluj payment packages
        $packageCount = PaymentPackage::count();
        if ($packageCount === 0) {
            $this->warn('⚠️  No payment packages found, seeding...');
            Artisan::call('db:seed', ['--class' => 'PaymentPackageSeeder']);
            $this->info('✅ Payment packages seeded');
        } else {
            $this->info("✅ Found {$packageCount} payment packages");
        }
        
        // Skontroluj ads
        $adCount = Ad::count();
        $this->info("✅ Found {$adCount} ads in database");
    }
    
    private function fixImagePaths($fix)
    {
        $this->info('🖼️  Fixing image paths...');
        
        $command = $fix ? 'fix:image-paths' : 'fix:image-paths --dry-run';
        $exitCode = Artisan::call($command);
        
        if ($exitCode === 0) {
            $this->info('✅ Image paths processed');
        } else {
            $this->error('❌ Image path fixing failed');
        }
    }
    
    private function checkImageFiles($fix)
    {
        $this->info('🔍 Checking image files...');
        
        $command = $fix ? 'check:image-files --fix' : 'check:image-files';
        $exitCode = Artisan::call($command);
        
        if ($exitCode === 0) {
            $this->info('✅ Image files checked');
        } else {
            $this->error('❌ Image file check failed');
        }
    }
    
    private function ensureSymlinks()
    {
        $this->info('🔗 Ensuring symbolic links...');
        
        $publicStorage = public_path('storage');
        $storagePublic = storage_path('app/public');
        
        if (!File::exists($publicStorage)) {
            if (File::exists($storagePublic)) {
                try {
                    if (PHP_OS_FAMILY === 'Windows') {
                        // Windows junction
                        $cmd = 'mklink /J "' . $publicStorage . '" "' . $storagePublic . '"';
                        exec($cmd, $output, $return);
                    } else {
                        // Unix symlink
                        symlink($storagePublic, $publicStorage);
                    }
                    $this->info('✅ Created storage symlink');
                } catch (\Exception $e) {
                    $this->error('❌ Failed to create storage symlink: ' . $e->getMessage());
                }
            }
        } else {
            $this->info('✅ Storage symlink already exists');
        }
    }
    
    private function finalCheck()
    {
        $this->info('🔍 Final diagnostic check...');
        
        $problematicFiles = [
            'verification_50758.jpg',
            'verification_50773.jpg', 
            'verification_50841.jpg',
            'verification_50790.jpg',
            'verification_50671.jpg'
        ];
        
        $foundFiles = 0;
        foreach ($problematicFiles as $filename) {
            if (File::exists(storage_path("app/public/ads/verification/{$filename}"))) {
                $foundFiles++;
            }
        }
        
        $this->info("✅ Found {$foundFiles} out of " . count($problematicFiles) . " problematic files");
        
        if ($foundFiles < count($problematicFiles)) {
            $this->warn('⚠️  Some problematic files are still missing');
            $this->line('You may need to run: php artisan debug:image-files');
        }
    }
} 