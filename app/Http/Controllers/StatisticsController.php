<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Získaj všetky inzeráty používateľa
        $userAds = Ad::where('user_id', $user->id)->get();
        
        // Základné štatistiky
        $totalViews = $userAds->sum('views');
        $totalClicks = $userAds->sum('clicks');
        $clickRate = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 1) : 0;
        
        // Štatistiky pre TOP inzeráty
        $topAds = $userAds->where('top_ad', true);
        $topAdsViews = $topAds->sum('views');
        $topAdsClicks = $topAds->sum('clicks');
        
        // Štatistiky pre zvýraznené inzeráty
        $featuredAds = $userAds->where('featured', true);
        $featuredViews = $featuredAds->sum('views');
        
        // Mesačný rast (simulovaný - môžeme neskôr pridať tracking)
        $monthlyGrowth = $totalViews > 0 ? rand(5, 25) : 0;
        
        // Simulované denné dáta za posledných 7 dní
        $dailyViews = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyViews[] = [
                'date' => $date->format('Y-m-d'),
                'views' => $this->simulateDailyViews($totalViews, $i),
                'clicks' => $this->simulateDailyClicks($totalClicks, $i)
            ];
        }
        
        // Štatistiky podľa krajín (simulované)
        $countryStats = $this->generateCountryStats($totalViews);
        
        // Štatistiky podľa zariadení (simulované)
        $deviceStats = $this->generateDeviceStats($totalViews);
        
        // Hodinové štatistiky (simulované)
        $hourlyStats = $this->generateHourlyStats($totalViews);
        
        $statistics = [
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'click_rate' => $clickRate,
            'avg_time_on_page' => $this->calculateAvgTimeOnPage($totalViews),
            'top_ads_views' => $topAdsViews,
            'top_ads_clicks' => $topAdsClicks,
            'featured_views' => $featuredViews,
            'monthly_growth' => $monthlyGrowth,
            'daily_views' => $dailyViews,
            'country_stats' => $countryStats,
            'device_stats' => $deviceStats,
            'hourly_stats' => $hourlyStats,
            'ads_count' => $userAds->count(),
            'active_ads' => $userAds->where('status', 'active')->count(),
            'top_ads_count' => $topAds->count(),
            'featured_ads_count' => $featuredAds->count(),
        ];

        return view('statistics.index', compact('statistics', 'userAds'));
    }
    
    private function simulateDailyViews($totalViews, $daysAgo)
    {
        if ($totalViews == 0) return 0;
        
        // Simuluj denné zobrazenia na základe celkových zobrazení
        $baseDaily = $totalViews / 30; // Priemerné denné zobrazenia za mesiac
        $variation = rand(70, 130) / 100; // Variácia ±30%
        
        return max(0, round($baseDaily * $variation));
    }
    
    private function simulateDailyClicks($totalClicks, $daysAgo)
    {
        if ($totalClicks == 0) return 0;
        
        $baseDaily = $totalClicks / 30;
        $variation = rand(70, 130) / 100;
        
        return max(0, round($baseDaily * $variation));
    }
    
    private function generateCountryStats($totalViews)
    {
        if ($totalViews == 0) {
            return [];
        }
        
        $countries = [
            ['country' => 'Slovensko', 'code' => 'SK', 'percentage' => 55],
            ['country' => 'Česká republika', 'code' => 'CZ', 'percentage' => 25],
            ['country' => 'Rakúsko', 'code' => 'AT', 'percentage' => 10],
            ['country' => 'Nemecko', 'code' => 'DE', 'percentage' => 6],
            ['country' => 'Poľsko', 'code' => 'PL', 'percentage' => 3],
            ['country' => 'Maďarsko', 'code' => 'HU', 'percentage' => 1],
        ];
        
        foreach ($countries as &$country) {
            $country['views'] = round($totalViews * ($country['percentage'] / 100));
        }
        
        return $countries;
    }
    
    private function generateDeviceStats($totalViews)
    {
        if ($totalViews == 0) {
            return [];
        }
        
        $devices = [
            ['device' => 'Mobil', 'percentage' => 65],
            ['device' => 'Desktop', 'percentage' => 25],
            ['device' => 'Tablet', 'percentage' => 10],
        ];
        
        foreach ($devices as &$device) {
            $device['views'] = round($totalViews * ($device['percentage'] / 100));
        }
        
        return $devices;
    }
    
    private function generateHourlyStats($totalViews)
    {
        $hourlyStats = [];
        
        for ($hour = 0; $hour < 24; $hour++) {
            // Simuluj aktivitu podľa hodiny (večer viac ako ráno)
            $multiplier = match(true) {
                $hour >= 0 && $hour < 6 => 0.3,   // Noc
                $hour >= 6 && $hour < 9 => 0.6,   // Ráno
                $hour >= 9 && $hour < 17 => 0.8,  // Deň
                $hour >= 17 && $hour < 22 => 1.2, // Večer
                default => 0.9                     // Neskoro večer
            };
            
            $views = $totalViews > 0 ? round(($totalViews / 24) * $multiplier) : 0;
            
            $hourlyStats[] = [
                'hour' => sprintf('%02d:00', $hour),
                'views' => $views
            ];
        }
        
        return $hourlyStats;
    }
    
    private function calculateAvgTimeOnPage($totalViews)
    {
        if ($totalViews == 0) return '0:00';
        
        // Simuluj priemerný čas na základe počtu zobrazení
        $seconds = match(true) {
            $totalViews < 100 => rand(45, 90),
            $totalViews < 1000 => rand(90, 180),
            $totalViews < 5000 => rand(120, 240),
            default => rand(150, 300)
        };
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }
} 