<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EroticClub;
use App\Models\BlogPost;
use App\Models\Ad;
use App\Models\SupportTicket;
use App\Models\AdPayment;
use App\Models\CustomerReport;
use App\Models\AdReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'usersCount' => User::count(),
            'clubsCount' => EroticClub::count(),
            'articlesCount' => BlogPost::count(),
            'adsCount' => Ad::count(),
            'activeAdsCount' => Ad::where('status', 'active')->count(),
            'supportTicketsCount' => SupportTicket::count(),
            'openSupportTicketsCount' => SupportTicket::where('status', 'open')->count(),
            'paymentsCount' => AdPayment::count(),
            'customerReportsCount' => CustomerReport::count(),
            'adReportsCount' => AdReport::count(),
        ];

        return view('admin-page.dashboard', $data);
    }

    public function profile()
    {
        return view('admin-page.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => strtolower($request->email),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profil bol úspešne aktualizovaný.');
    }

    public function updateExtendedProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'about' => 'nullable|string|max:1000',
        ]);

        $data = $request->only(['phone', 'city', 'about']);

        // Handle photo upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            try {
                // Create directory if not exists (private storage)
                $uploadDir = storage_path('app/private/profile-photos');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate unique filename
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $relativePath = 'profile-photos/' . $filename;
                $fullPath = $uploadDir . '/' . $filename;
                
                // Move uploaded file
                if ($file->move($uploadDir, $filename)) {
                    $data['photo'] = $relativePath;
                    
                    // Delete old photo only after successful upload
                    $oldPhoto = $user->photo;
                    if ($oldPhoto && is_string($oldPhoto) && strlen(trim($oldPhoto)) > 0) {
                        // Support both legacy public and new private paths
                        $oldFullPathPublic = storage_path('app/public/' . $oldPhoto);
                        $oldFullPathPrivate = storage_path('app/private/' . $oldPhoto);
                        if (file_exists($oldFullPathPublic) && is_file($oldFullPathPublic)) {
                            unlink($oldFullPathPublic);
                        }
                        if (file_exists($oldFullPathPrivate) && is_file($oldFullPathPrivate)) {
                            unlink($oldFullPathPrivate);
                        }
                    }
                } else {
                    throw new \Exception('Failed to move uploaded file');
                }
                
            } catch (\Exception $e) {
                \Log::error('Failed to upload profile photo: ' . $e->getMessage(), ['user_id' => $user->id]);
                return redirect()->route('admin.profile')->withErrors(['photo' => 'Chyba pri nahrávaní obrázka.']);
            }
        }

        $user->update($data);

        return redirect()->route('admin.profile')->with('success', 'Rozšírené nastavenia boli úspešne aktualizované.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Aktuálne heslo je nesprávne.'], 'updatePassword');
        }

        $user->update([
            'password' => \Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Heslo bolo úspešne zmenené.');
    }

    public function kluby()
    {
        $clubs = EroticClub::paginate(15);
        return view('admin-page.kluby.index', compact('clubs'));
    }

    public function pouzivatelia()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function inzeraty()
    {
        $ads = Ad::with('user')->paginate(15);
        
        $stats = [
            'total_ads' => Ad::count(),
            'active_ads' => Ad::where('status', 'active')->count(),
            'pending_ads' => Ad::where('status', 'pending')->count(),
            'inactive_ads' => Ad::where('status', 'inactive')->count(),
        ];
        
        return view('admin.ads.index', compact('ads', 'stats'));
    }

    public function clanky()
    {
        $articles = BlogPost::paginate(15);
        
        $stats = [
            'total_articles' => BlogPost::count(),
            'published_articles' => BlogPost::where('is_published', true)->count(),
            'draft_articles' => BlogPost::where('is_published', false)->count(),
        ];
        
        return view('admin.blog.index', compact('articles', 'stats'));
    }

    public function platby()
    {
        $payments = AdPayment::with(['user', 'ad', 'paymentPackage'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Štatistiky pre view
        $totalInvoices = AdPayment::count();
        $paidInvoices = AdPayment::where('status', 'completed')->count();
        $pendingInvoices = AdPayment::where('status', 'pending')->count();
        $overdueInvoices = AdPayment::where('status', 'failed')->count();
        $totalRevenue = AdPayment::where('status', 'completed')->sum('amount');
        $pendingRevenue = AdPayment::where('status', 'pending')->sum('amount');
        
        // Vytvoríme invoices z payments s potrebnými dátumami
        $invoices = $payments->map(function ($payment) {
            $payment->issue_date = $payment->created_at;
            $payment->due_date = $payment->created_at ? $payment->created_at->addDays(30) : null;
            return $payment;
        });
        
        return view('admin.payments.index', compact(
            'payments', 
            'invoices',
            'totalInvoices',
            'paidInvoices', 
            'pendingInvoices',
            'overdueInvoices',
            'totalRevenue',
            'pendingRevenue'
        ));
    }

    public function statistiky()
    {
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        
        $data = [
            // Základné počty
            'totalUsers' => User::count(),
            'totalAds' => Ad::count(),
            'totalClubs' => EroticClub::count(),
            'totalTickets' => SupportTicket::count(),
            'totalArticles' => BlogPost::count(),
            'totalPayments' => AdPayment::count(),
            
            // Aktívni používatelia (s aktivitou za posledných 30 dní)
            'activeUsers' => User::where(function($query) use ($thirtyDaysAgo) {
                $query->where('created_at', '>=', $thirtyDaysAgo)
                      ->orWhereHas('ads', function($q) use ($thirtyDaysAgo) {
                          $q->where('created_at', '>=', $thirtyDaysAgo);
                      })
                      ->orWhereHas('supportTickets', function($q) use ($thirtyDaysAgo) {
                          $q->where('created_at', '>=', $thirtyDaysAgo);
                      });
            })->count(),
            
            // Nové za posledných 30 dní
            'newUsersLast30Days' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'newAdsLast30Days' => Ad::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'newTicketsLast30Days' => SupportTicket::where('created_at', '>=', $thirtyDaysAgo)->count(),
            
            // Príjmy
            'totalRevenue' => AdPayment::where('status', 'completed')->sum('amount'),
            'revenueThisMonth' => AdPayment::where('status', 'completed')
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->sum('amount'),
            
            // Štatistiky inzerátov
            'activeAds' => Ad::where('status', 'active')->count(),
            'pendingAds' => Ad::where('status', 'pending')->count(),
            'expiredAds' => Ad::where('subscription_expires_at', '<', $now)->count(),
            
            // Inzeráty podľa statusu
            'adsByStatus' => [
                'active' => Ad::where('status', 'active')->count(),
                'pending' => Ad::where('status', 'pending')->count(),
                'inactive' => Ad::where('status', 'inactive')->count(),
                'rejected' => Ad::where('status', 'rejected')->count(),
            ],
            
            // Support tickets podľa statusu
            'ticketsByStatus' => [
                'open' => SupportTicket::where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved' => SupportTicket::where('status', 'resolved')->count(),
                'closed' => SupportTicket::where('status', 'closed')->count(),
            ],
            
            // Štatistiky platieb
            'completedPayments' => AdPayment::where('status', 'completed')->count(),
            'pendingPayments' => AdPayment::where('status', 'pending')->count(),
            'failedPayments' => AdPayment::where('status', 'failed')->count(),
            
            // Top mestá podľa počtu inzerátov
            'topCities' => $this->getTopCities(),
            
            // Mesačné dáta pre grafy (posledných 12 mesiacov)
            'monthlyData' => $this->getMonthlyData(),
            'monthlyStats' => $this->getMonthlyStats(),
            'monthlyRevenue' => $this->getMonthlyRevenue(),
            
            // Dáta pre grafy vo view
            'registrationsLast7Days' => $this->getRegistrationsLast7Days(),
            'adsByCategory' => $this->getAdsByCategory(),
            'recentUsers' => $this->getRecentUsers(),
        ];
        
        return view('admin.statistics.index', $data);
    }

    private function getMonthlyData()
    {
        $months = [];
        $now = Carbon::now();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'users' => User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'ads' => Ad::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'revenue' => AdPayment::where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
            ];
        }
        
        return $months;
    }

    public function nahlasenia()
    {
        $customerReports = CustomerReport::paginate(15);
        return view('admin.customer-reports.index', compact('customerReports'));
    }



    private function getTopCities()
    {
        // Získame top 10 miest podľa počtu inzerátov
        return User::select('city', \DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getMonthlyStats()
    {
        $months = [];
        $now = Carbon::now();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'users' => User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'ads' => Ad::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'payments' => AdPayment::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }
        
        return $months;
    }

    private function getMonthlyRevenue()
    {
        $months = [];
        $now = Carbon::now();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'revenue' => AdPayment::where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
            ];
        }
        
        return $months;
    }

    private function getRegistrationsLast7Days()
    {
        $registrations = collect();
        $now = Carbon::now();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $count = User::whereDate('created_at', $date->format('Y-m-d'))->count();
            $registrations->put($date->format('d.m'), $count);
        }
        
        return $registrations;
    }

    private function getAdsByCategory()
    {
        return Ad::select('ad_type', \DB::raw('count(*) as count'))
            ->whereNotNull('ad_type')
            ->where('ad_type', '!=', '')
            ->groupBy('ad_type')
            ->orderBy('count', 'desc')
            ->pluck('count', 'ad_type')
            ->mapWithKeys(function ($count, $type) {
                $labels = [
                    'zena' => 'Žena',
                    'muz' => 'Muž', 
                    'par' => 'Pár',
                    'trans' => 'Trans',
                    'klub' => 'Klub'
                ];
                return [$labels[$type] ?? $type => $count];
            });
    }

    private function getRecentUsers()
    {
        return User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
} 