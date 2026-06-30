<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ClearAllCache extends Command
{
    protected $signature = 'cache:clear-all {--force : Force clear all cache}';
    protected $description = 'Clear all Laravel cache (config, route, view, application)';

    public function handle()
    {
        $this->info('🔄 Clearing all Laravel cache...');

        // Clear application cache
        $this->info('1. Clearing application cache...');
        Artisan::call('cache:clear');
        $this->info('✅ Application cache cleared');

        // Clear configuration cache
        $this->info('2. Clearing configuration cache...');
        Artisan::call('config:clear');
        $this->info('✅ Configuration cache cleared');

        // Clear route cache
        $this->info('3. Clearing route cache...');
        Artisan::call('route:clear');
        $this->info('✅ Route cache cleared');

        // Clear view cache
        $this->info('4. Clearing view cache...');
        Artisan::call('view:clear');
        $this->info('✅ View cache cleared');

        // Clear compiled classes
        $this->info('5. Clearing compiled classes...');
        if (function_exists('opcache_reset')) {
            opcache_reset();
            $this->info('✅ OPcache cleared');
        }

        // Manually delete cache files
        $this->info('6. Manually clearing cache files...');
        $cacheDir = storage_path('framework/cache');
        if (File::exists($cacheDir)) {
            File::cleanDirectory($cacheDir);
            $this->info('✅ Cache directory cleaned');
        }

        // Re-cache everything
        if ($this->option('force')) {
            $this->info('7. Re-caching configuration...');
            Artisan::call('config:cache');
            $this->info('✅ Configuration re-cached');

            $this->info('8. Re-caching routes...');
            Artisan::call('route:cache');
            $this->info('✅ Routes re-cached');
        }

        $this->info('🎉 All cache cleared successfully!');
        
        // Show route info
        $this->info('📋 Checking admin.settings routes...');
        Artisan::call('route:list', ['--name' => 'admin.settings']);
        $this->info(Artisan::output());

        return 0;
    }
} 