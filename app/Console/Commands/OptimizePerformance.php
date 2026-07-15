<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\Ad;
use App\Models\PaymentPackage;

class OptimizePerformance extends Command
{
    protected $signature = 'optimize:performance {--full : Run full optimization including database}';
    protected $description = 'Comprehensive performance optimization for DiskretneDnes.sk';

    public function handle()
    {
        $this->info('🚀 Starting performance optimization...');
        
        if ($this->option('full')) {
            $this->optimizeDatabase();
        }
        
        $this->optimizeCache();
        $this->optimizeLaravel();
        $this->generateCriticalCache();
        $this->checkConfiguration();
        
        $this->info('✅ Performance optimization completed!');
        return 0;
    }
    
    private function optimizeDatabase()
    {
        $this->info('🗄️  Optimizing database...');
        
        // Analyze tables
        $this->line('Analyzing tables...');
        DB::statement('ANALYZE TABLE ads, users, ad_payments, payment_packages');
        
        // Optimize tables
        $this->line('Optimizing tables...');
        DB::statement('OPTIMIZE TABLE ads, users, ad_payments, payment_packages');
        
        // Check for missing indexes
        $this->checkIndexes();
        
        $this->info('✅ Database optimization completed');
    }
    
    private function checkIndexes()
    {
        $this->line('Checking for missing indexes...');
        
        // Check slow queries
        $slowQueries = DB::select("
            SELECT sql_text, mean_timer, count_star
            FROM performance_schema.events_statements_summary_by_digest
            WHERE schema_name = ?
            AND mean_timer > 1000000000
            ORDER BY mean_timer DESC
            LIMIT 5
        ", [config('database.connections.mysql.database')]);
        
        if (!empty($slowQueries)) {
            $this->warn('⚠️  Found slow queries:');
            foreach ($slowQueries as $query) {
                $this->line('- ' . substr($query->sql_text, 0, 100) . '...');
            }
        }
    }
    
    private function optimizeCache()
    {
        $this->info('🧠 Optimizing cache...');
        
        // Clear old cache
        Cache::flush();
        
        // Warm up critical cache
        $this->line('Warming up critical cache...');
        
        // Pre-cache home page data
        cache()->remember('home_stats', 1800, function() {
            return [
                'total_ads' => Ad::active()->withActiveSubscription()->count(),
                'ad_types' => Ad::active()->withActiveSubscription()
                    ->select('ad_type', DB::raw('count(*) as count'))
                    ->groupBy('ad_type')
                    ->pluck('count', 'ad_type')
                    ->toArray(),
                'cities' => Ad::active()->withActiveSubscription()
                    ->distinct('city')
                    ->pluck('city')
                    ->sort()
                    ->toArray()
            ];
        });
        
        // Pre-cache payment packages
        cache()->remember('payment_packages', 3600, function() {
            return PaymentPackage::active()->ordered()->get();
        });
        
        $this->info('✅ Cache optimization completed');
    }
    
    private function optimizeLaravel()
    {
        $this->info('⚡ Optimizing Laravel...');
        
        // Clear and cache configs
        Artisan::call('config:cache');
        $this->line('✅ Config cached');
        
        // Clear and cache routes
        Artisan::call('route:cache');
        $this->line('✅ Routes cached');
        
        // Clear and cache views
        Artisan::call('view:cache');
        $this->line('✅ Views cached');
        
        // Clear and cache events
        Artisan::call('event:cache');
        $this->line('✅ Events cached');
        
        $this->info('✅ Laravel optimization completed');
    }
    
    private function generateCriticalCache()
    {
        $this->info('🔥 Generating critical cache...');
        
        // Pre-cache top ads
        cache()->remember('top_ads_carousel', 1800, function() {
            return Ad::with(['user:id,name,email'])
                ->select([
                    'id', 'user_id', 'nickname', 'age', 'city', 'street', 'ad_type', 'offer_type',
                    'verification_photo', 'gallery_photos', 'phone_verified', 'top_ad', 'featured', 'views',
                    'created_at', 'updated_at'
                ])
                ->active()
                ->withActiveSubscription()
                ->where(function($q) {
                    $q->where('top_ad', true)
                      ->orWhere('featured', true);
                })
                ->latest()
                ->take(10)
                ->get();
        });
        
        // Pre-cache cities with counts
        cache()->remember('cities_with_counts', 3600, function() {
            return Ad::active()
                ->withActiveSubscription()
                ->select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->having('count', '>', 0)
                ->orderBy('count', 'desc')
                ->get()
                ->pluck('count', 'city')
                ->toArray();
        });
        
        $this->info('✅ Critical cache generated');
    }
    
    private function checkConfiguration()
    {
        $this->info('⚙️  Checking configuration...');
        
        // Check environment
        $env = config('app.env');
        if ($env !== 'production') {
            $this->warn("⚠️  Environment is '{$env}' - should be 'production' for optimal performance");
        }
        
        // Check debug mode
        if (config('app.debug')) {
            $this->warn('⚠️  Debug mode is enabled - should be disabled in production');
        }
        
        // Check cache driver
        $cacheDriver = config('cache.default');
        if (!in_array($cacheDriver, ['redis', 'memcached'])) {
            $this->warn("⚠️  Cache driver is '{$cacheDriver}' - Redis or Memcached recommended");
        }
        
        // Check session driver
        $sessionDriver = config('session.driver');
        if (!in_array($sessionDriver, ['redis', 'memcached'])) {
            $this->warn("⚠️  Session driver is '{$sessionDriver}' - Redis or Memcached recommended");
        }
        
        $this->info('✅ Configuration check completed');
    }
} 