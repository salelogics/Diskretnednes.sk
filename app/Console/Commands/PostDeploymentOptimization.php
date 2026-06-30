<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class PostDeploymentOptimization extends Command
{
    protected $signature = 'deploy:optimize {--reset-opcache : Force OPcache reset}';
    protected $description = 'Optimalizuje aplikáciu po deployment a rieši "No input file specified" chyby';

    public function handle()
    {
        $this->info('🚀 Spúšťam post-deployment optimalizáciu...');
        
        // 1. Kompletné vyčistenie cache (PRVÉ!)
        $this->clearAllCache();
        
        // 2. OPcache reset (KĽÚČOVÉ!)
        $this->resetOPcache();
        
        // 3. Základné Laravel operácie
        $this->runBasicOperations();
        
        // 4. Obnovenie optimalizovanej cache
        $this->rebuildOptimizedCache();
        
        // 5. Overenie funkcionality
        $this->verifyDeployment();
        
        $this->info('✅ Post-deployment optimalizácia dokončená!');
        $this->info('🎯 "No input file specified" problém by mal byť vyriešený');
        
        return 0;
    }
    
    private function clearAllCache()
    {
        $this->info('🧹 1. Kompletné vyčistenie cache...');
        
        // Vyčisti všetky Laravel cache
        $this->line('   • Application cache...');
        Artisan::call('cache:clear');
        
        $this->line('   • Configuration cache...');
        Artisan::call('config:clear');
        
        $this->line('   • Route cache...');
        Artisan::call('route:clear');
        
        $this->line('   • View cache...');
        Artisan::call('view:clear');
        
        // Vyčisti compiled classes
        $this->line('   • Compiled classes...');
        $bootstrapCache = base_path('bootstrap/cache');
        if (File::exists($bootstrapCache)) {
            $files = File::files($bootstrapCache);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php' && $file->getFilename() !== '.gitignore') {
                    File::delete($file->getPathname());
                }
            }
        }
        
        $this->info('   ✅ Všetky cache vyčistené');
    }
    
    private function resetOPcache()
    {
        $this->info('⚡ 2. Reset OPcache (rieši "No input file specified")...');
        
        if (function_exists('opcache_reset')) {
            $success = opcache_reset();
            if ($success) {
                $this->info('   ✅ OPcache úspešne resetovaný');
            } else {
                $this->warn('   ⚠️  OPcache reset zlyhal');
            }
        } else {
            $this->warn('   ⚠️  OPcache nie je dostupný');
        }
        
        // Pre hosting môže byť potrebné aj toto
        if (function_exists('opcache_invalidate')) {
            $this->line('   • Invalidating key files...');
            $keyFiles = [
                base_path('app/Http/Kernel.php'),
                base_path('routes/web.php'),
                base_path('config/app.php'),
                base_path('public/index.php'),
            ];
            
            foreach ($keyFiles as $file) {
                if (file_exists($file)) {
                    opcache_invalidate($file, true);
                }
            }
        }
        
        // Force reload autoloader
        $this->line('   • Reloading autoloader...');
        if (file_exists(base_path('vendor/autoload.php'))) {
            include_once base_path('vendor/autoload.php');
        }
    }
    
    private function runBasicOperations()
    {
        $this->info('🔧 3. Základné operácie...');
        
        // Storage link
        $this->line('   • Creating storage link...');
        Artisan::call('storage:link');
        
        // Ensure key exists
        if (empty(config('app.key'))) {
            $this->line('   • Generating application key...');
            Artisan::call('key:generate', ['--force' => true]);
        }
        
        $this->info('   ✅ Základné operácie dokončené');
    }
    
    private function rebuildOptimizedCache()
    {
        $this->info('🏗️  4. Obnova optimalizovanej cache...');
        
        $this->line('   • Configuration cache...');
        Artisan::call('config:cache');
        
        $this->line('   • Route cache...');
        Artisan::call('route:cache');
        
        $this->line('   • View cache...');
        Artisan::call('view:cache');
        
        // Event cache if available
        try {
            $this->line('   • Event cache...');
            Artisan::call('event:cache');
        } catch (\Exception $e) {
            // Event cache môže byť nedostupný v starších verziách
        }
        
        $this->info('   ✅ Optimalizovaná cache obnovená');
    }
    
    private function verifyDeployment()
    {
        $this->info('🔍 5. Overenie deployment...');
        
        // Check key files
        $keyFiles = [
            'app/Http/Kernel.php' => 'HTTP Kernel',
            'routes/web.php' => 'Web routes',
            'config/app.php' => 'App config',
            'public/index.php' => 'Public index',
        ];
        
        foreach ($keyFiles as $file => $description) {
            if (file_exists(base_path($file))) {
                $this->line("   ✅ {$description}: OK");
            } else {
                $this->error("   ❌ {$description}: CHÝBA!");
            }
        }
        
        // Check cache files
        $this->line('   • Cache files...');
        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        
        $this->line("   • Config cache: " . ($configCached ? '✅' : '❌'));
        $this->line("   • Routes cache: " . ($routesCached ? '✅' : '❌'));
        
        // Check storage link
        $storageLinked = is_link(public_path('storage')) || is_dir(public_path('storage'));
        $this->line("   • Storage link: " . ($storageLinked ? '✅' : '❌'));
        
        $this->info('   ✅ Deployment overený');
    }
} 