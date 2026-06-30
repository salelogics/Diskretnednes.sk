<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\Club;
use App\Models\BlogPost;
use App\Models\AdPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        // Základné štatistiky
        $totalUsers = User::count();
        $totalAds = Ad::count();
        $totalClubs = Club::count();
        $totalTickets = SupportTicket::count();
        $totalArticles = BlogPost::count();
        $totalPayments = AdPayment::where('status', 'completed')->count();
        
        // Štatistiky za posledných 30 dní
        $last30Days = Carbon::now()->subDays(30);
        
        $newUsersLast30Days = User::where('created_at', '>=', $last30Days)->count();
        $newAdsLast30Days = Ad::where('created_at', '>=', $last30Days)->count();
        $newTicketsLast30Days = SupportTicket::where('created_at', '>=', $last30Days)->count();
        
        // Mesačné štatistiky pre grafy (posledných 12 mesiacov)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'ads' => Ad::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'payments' => AdPayment::where('status', 'completed')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count(),
            ];
        }
        
        // Štatistiky inzerátov podľa statusu
        $adsByStatus = [
            'active' => Ad::where('status', 'active')->count(),
            'pending' => Ad::where('status', 'pending')->count(),
            'inactive' => Ad::where('status', 'inactive')->count(),
            'rejected' => Ad::where('status', 'rejected')->count(),
        ];
        
        // Štatistiky support tiketov
        $ticketsByStatus = [
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];
        
        // Top 5 miest podľa počtu inzerátov
        $topCities = Ad::select('city', DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        // Príjmy za posledných 12 mesiacov
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = AdPayment::where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }
        
        $totalRevenue = AdPayment::where('status', 'completed')->sum('amount');
        
        return view('admin.statistics.index', compact(
            'totalUsers',
            'totalAds', 
            'totalClubs',
            'totalTickets',
            'totalArticles',
            'totalPayments',
            'newUsersLast30Days',
            'newAdsLast30Days', 
            'newTicketsLast30Days',
            'monthlyStats',
            'adsByStatus',
            'ticketsByStatus',
            'topCities',
            'monthlyRevenue',
            'totalRevenue'
        ));
    }

    /**
     * Zobrazenie Google Analytics štatistík
     */
    public function analytics()
    {
        // Načítame Google Analytics ID z databázy nastavení
        $googleAnalyticsId = \App\Models\Setting::get('google_analytics_id');
        
        // Inicializujeme Google Analytics Service
        $analyticsService = new \App\Services\GoogleAnalyticsService();
        
        // Získame reálne dáta z GA (ak je nastavené)
        $analyticsData = [
            'is_configured' => $analyticsService->isConfigured(),
            'visitors_and_pageviews' => $analyticsService->getTotalVisitorsAndPageViews(7),
            'most_visited_pages' => $analyticsService->getMostVisitedPages(10),
            'top_countries' => $analyticsService->getTopCountries(10),
            'top_browsers' => $analyticsService->getTopBrowsers(10),
            'top_referrers' => $analyticsService->getTopReferrers(10),
            'setup_instructions' => $analyticsService->getSetupInstructions(),
        ];
        
        return view('admin.statistics.analytics', [
            'googleAnalyticsId' => $googleAnalyticsId,
            'analyticsData' => $analyticsData
        ]);
    }
} 