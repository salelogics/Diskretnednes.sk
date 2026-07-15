<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdReport;
use App\Services\NotificationService;

class PublicAdsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        // Cache key based on request parameters
        $cacheKey = 'ads_listing_' . md5(serialize($request->all()));
        
        $query = Ad::with(['user:id,name,email'])
            ->select([
                'id', 'user_id', 'nickname', 'age', 'city', 'street', 'ad_type', 'offer_type',
                'verification_photo', 'gallery_photos', 'phone_verified', 'top_ad', 'views',
                'created_at', 'updated_at'
            ])
            ->active()
            ->withActiveSubscription()
            ->latest();

        // Multi-select filtrovanie podľa parametrov
        if ($request->filled('cities')) {
            $cities = is_array($request->cities) ? $request->cities : [$request->cities];
            $query->where(function($q) use ($cities) {
                foreach ($cities as $city) {
                    $q->orWhere('city', 'like', '%' . $city . '%');
                }
            });
        }

        if ($request->filled('ad_types')) {
            $adTypes = is_array($request->ad_types) ? $request->ad_types : [$request->ad_types];
            // Normalizácia: zluč starý typ 'individual' pod 'zena'
            $expandedTypes = [];
            foreach ($adTypes as $type) {
                if ($type === 'zena') {
                    $expandedTypes[] = 'zena';
                    $expandedTypes[] = 'individual';
                } else {
                    $expandedTypes[] = $type;
                }
            }
            $expandedTypes = array_unique($expandedTypes);
            $query->whereIn('ad_type', $expandedTypes);
        }

        if ($request->filled('offer_types')) {
            $offerTypes = is_array($request->offer_types) ? $request->offer_types : [$request->offer_types];
            $query->where(function($q) use ($offerTypes) {
                foreach ($offerTypes as $offerType) {
                    $q->orWhereJsonContains('offer_type', $offerType);
                }
            });
        }

        if ($request->filled('age_ranges')) {
            $ageRanges = is_array($request->age_ranges) ? $request->age_ranges : [$request->age_ranges];
            $query->where(function($q) use ($ageRanges) {
                foreach ($ageRanges as $range) {
                    if ($range === '65+') {
                        $q->orWhere('age', '>=', 65);
                    } else {
                        $parts = explode('-', $range);
                        if (count($parts) === 2) {
                            $q->orWhereBetween('age', [(int)$parts[0], (int)$parts[1]]);
                        }
                    }
                }
            });
        }

        // Backwards compatibility pre staré single-value filtre
        if ($request->filled('city') && !$request->filled('cities')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('ad_type') && !$request->filled('ad_types')) {
            // Normalizácia: ak je vybraná 'zena', zahrň aj legacy 'individual'
            if ($request->ad_type === 'zena') {
                $query->whereIn('ad_type', ['zena', 'individual']);
            } else {
                $query->where('ad_type', $request->ad_type);
            }
        }

        if ($request->filled('offer_type') && !$request->filled('offer_types')) {
            $query->whereJsonContains('offer_type', $request->offer_type);
        }

        if ($request->filled('age_from') && $request->filled('age_to') && !$request->filled('age_ranges')) {
            $query->whereBetween('age', [$request->age_from, $request->age_to]);
        }

        // Paginácia pre lepší výkon
        $ads = $query->paginate(20);

        // Načítanie topovaných inzerátov pre carousel
        $topAds = cache()->remember('top_ads_carousel_v3', 1800, function() {
            $ads = Ad::with(['user:id,name,email'])
                ->select([
                    'id', 'user_id', 'nickname', 'age', 'city', 'street', 'ad_type', 'offer_type',
                    'verification_photo', 'gallery_photos', 'phone_verified', 'top_ad', 'featured', 'views',
                    'created_at', 'updated_at', 'subscription_status', 'subscription_expires_at', 'status'
                ])
                ->where('status', 'active')  // Explicitne kontrolujeme status
                ->where('subscription_status', 'active')  // Explicitne kontrolujeme subscription
                ->where(function($q) {
                    $q->whereNull('subscription_expires_at')
                      ->orWhere('subscription_expires_at', '>', now());
                })
                ->where(function($q) {
                    $q->where('top_ad', true)
                      ->orWhere('featured', true);
                })
                ->latest()
                ->take(10)
                ->get();
                
            // Rozšírený debug log
            \Log::info('Top Ads Carousel Debug V3:', [
                'total_count' => $ads->count(),
                'top_ads_count' => $ads->where('top_ad', true)->count(),
                'featured_ads_count' => $ads->where('featured', true)->count(),
                'ads_ids' => $ads->pluck('id')->toArray(),
                'ads_details' => $ads->map(function($ad) {
                    return [
                        'id' => $ad->id,
                        'status' => $ad->status,
                        'subscription_status' => $ad->subscription_status,
                        'subscription_expires_at' => $ad->subscription_expires_at?->format('Y-m-d H:i:s'),
                        'top_ad' => $ad->top_ad,
                        'featured' => $ad->featured
                    ];
                })->toArray()
            ]);
            
            return $ads;
        });

        // Štatistiky - optimalizované s cache
        $citiesWithLabels = cache()->remember('cities_with_labels', 3600, function() {
            $cities = Ad::active()->withActiveSubscription()->distinct('city')->pluck('city')->sort();
            
            return $cities->mapWithKeys(function ($city) {
                $cityLabel = match($city) {
                    'bratislava' => 'Bratislava',
                    'kosice' => 'Košice',
                    'presov' => 'Prešov',
                    'zilina' => 'Žilina',
                    'banska-bystrica' => 'Banská Bystrica',
                    'nitra' => 'Nitra',
                    'trnava' => 'Trnava',
                    'martin' => 'Martin',
                    'trencin' => 'Trenčín',
                    'poprad' => 'Poprad',
                    'nove-zamky' => 'Nové Zámky',
                    'michalovce' => 'Michalovce',
                    'zvolen' => 'Zvolen',
                    'povazska-bystrica' => 'Považská Bystrica',
                    'prievidza' => 'Prievidza',
                    'levice' => 'Levice',
                    'spisska-nova-ves' => 'Spišská Nová Ves',
                    'bardejov' => 'Bardejov',
                    'humenne' => 'Humenné',
                    'lucenec' => 'Lučenec',
                    'ruzomberok' => 'Ružomberok',
                    'dolny-kubin' => 'Dolný Kubín',
                    'rimavska-sobota' => 'Rimavská Sobota',
                    'senica' => 'Senica',
                    'cadca' => 'Čadca',
                    'komarno' => 'Komárno',
                    'topol-cany' => 'Topoľčany',
                    'galanta' => 'Galanta',
                    'dunajska-streda' => 'Dunajská Streda',
                    'velky-krtis' => 'Veľký Krtíš',
                    'liptovsky-mikulas' => 'Liptovský Mikuláš',
                    'partizanske' => 'Partizánske',
                    'pezinok' => 'Pezinok',
                    'malacky' => 'Malacky',
                    'senec' => 'Senec',
                    'ine' => 'Iné',
                    default => ucfirst($city)
                };
                return [$city => $cityLabel];
            });
        });

        $stats = cache()->remember('home_stats_v2', 1800, function() {
            // Zjednotená štatistika typov: 'individual' sa zlučuje pod 'zena'
            $baseTypes = ['zena', 'muz', 'trans', 'par', 'klub'];
            $adTypesWithCounts = [];
            foreach ($baseTypes as $base) {
                if ($base === 'zena') {
                    $count = Ad::active()->withActiveSubscription()
                        ->whereIn('ad_type', ['zena', 'individual'])
                        ->count();
                } else {
                    $count = Ad::active()->withActiveSubscription()
                        ->where('ad_type', $base)
                        ->count();
                }
                if ($count > 0) {
                    $adTypesWithCounts[$base] = $count;
                }
            }

            // Pre offer_types musíme použiť iný prístup, pretože je to JSON array
            $offerTypesWithCounts = [];
            $allOfferTypes = ['stretnutie-u-mna', 'stretnutie-u-teba', 'masaz'];
            
            foreach ($allOfferTypes as $type) {
                $count = Ad::active()->withActiveSubscription()
                    ->whereJsonContains('offer_type', $type)
                    ->count();
                if ($count > 0) {
                    $offerTypesWithCounts[$type] = $count;
                }
            }

            return [
                'total_ads' => Ad::active()->withActiveSubscription()->count(),
                'ad_types' => $adTypesWithCounts,
                'offer_types' => $offerTypesWithCounts
            ];
        });
        
        $stats['cities'] = $citiesWithLabels;

        // AJAX response pre filtrovanie
        if ($request->wantsJson() || $request->ajax()) {
            $adsHtml = view('partials.ad-grid', compact('ads'))->render();
            
            // Render pagination HTML
            $paginationHtml = '';
            if ($ads->hasPages()) {
                $paginationHtml = $ads->appends(request()->query())->links()->render();
            }
            
            return response()->json([
                'ads_html' => $adsHtml,
                'pagination_html' => $paginationHtml,
                'total' => $ads->total(),
                'current_page' => $ads->currentPage(),
                'last_page' => $ads->lastPage()
            ]);
        }

        return view('pages.home', compact('ads', 'stats', 'topAds'));
    }

    public function tantra(Request $request)
    {
        $query = Ad::with('user')
            ->active()
            ->withActiveSubscription()
            ->where(function($q) {
                $q->whereJsonContains('offer_type', 'masaz')
                  ->orWhere('description', 'like', '%tantra%')
                  ->orWhere('nickname', 'like', '%tantra%');
            })
            ->latest();

        // Paginácia pre lepší výkon
        $ads = $query->paginate(20);

        return view('pages.tantra', compact('ads'));
    }

    public function show($id)
    {
        $ad = Ad::with('user')
            ->active()
            ->withActiveSubscription()
            ->findOrFail($id);

        // Zvýšenie počtu zobrazení
        $ad->increment('views');

        return view('pages.ad-detail', compact('ad'));
    }

    public function incrementClick($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->increment('clicks');

        return response()->json(['success' => true]);
    }

    public function report(Request $request, $id)
    {
        \Log::info('=== AD REPORT STARTED ===', [
            'ad_id' => $id,
            'request_data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson()
        ]);

        $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255'
        ]);

        \Log::info('AD REPORT: Validation passed', [
            'reason' => $request->reason,
            'details' => $request->details,
            'email' => $request->email
        ]);

        $ad = Ad::findOrFail($id);

        // Kontrola či už nebol reportovaný z tej istej IP v posledných 24 hodinách
        $existingReport = AdReport::where('ad_id', $id)
            ->where('reporter_ip', $request->ip())
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($existingReport) {
            \Log::info('AD REPORT: Duplicate report blocked', [
                'ad_id' => $id,
                'ip' => $request->ip(),
                'existing_report_id' => $existingReport->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Tento inzerát ste už nahlásili v posledných 24 hodinách.'
            ], 429);
        }

        $report = AdReport::create([
            'ad_id' => $id,
            'reason' => $request->reason,
            'details' => $request->details,
            'reporter_ip' => $request->ip(),
            'reporter_email' => $request->email,
            'status' => 'pending'
        ]);

        \Log::info('AD REPORT: Report created in database', [
            'report_id' => $report->id,
            'ad_id' => $id
        ]);

        // PRIDANÉ: Pošli email notifikáciu adminom
        try {
            \Log::info('AD REPORT: Starting email notification', [
                'report_id' => $report->id,
                'notification_service' => class_basename($this->notificationService)
            ]);
            
            $this->notificationService->newAdReport($report);
            
            \Log::info('AD REPORT: Email notification sent successfully', [
                'report_id' => $report->id,
                'ad_id' => $id,
                'reason' => $request->reason
            ]);
        } catch (\Exception $e) {
            \Log::error('AD REPORT: Failed to send email notification', [
                'report_id' => $report->id,
                'ad_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        \Log::info('=== AD REPORT COMPLETED ===', [
            'report_id' => $report->id,
            'success' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ďakujeme za nahlásenie. Váš report bol odoslaný na preverenie.'
        ]);
    }
} 