<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Získanie užívateľských štatistík
        $userAds = Ad::where('user_id', $user->id)->get();
        $userPayments = AdPayment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();
        
        // Výpočet štatistík
        $stats = [
            'total_ads' => $userAds->count(),
            'active_ads' => $userAds->where('status', 'active')->count(),
            'views' => $userAds->sum('views'),
            'clicks' => $userAds->sum('clicks'),
            'total_spent' => $userPayments->sum('amount'),
            'monthly_spent' => $userPayments->where('created_at', '>=', now()->startOfMonth())->sum('amount')
        ];
        
        // 🔥 NOVÁ LOGIKA: Kontrola predplatného
        $subscriptionAlert = null;
        
        // Nájdeme inzeráty s aktívnym predplatným ktoré sa čoskoro končí alebo už expirovali
        $expiringAds = $userAds->filter(function($ad) {
            if (!$ad->subscription_expires_at) {
                return false;
            }
            
            // Počítame dni až do expirácie (môže byť záporné ak už expiroval)
            $daysToExpiry = now()->diffInDays($ad->subscription_expires_at, false); // false = môže byť záporné
            
            // Zobrazíme ak končí do 7 dní alebo už expiroval (ale max 3 dni po expirácii)
            return $daysToExpiry <= 7 && $daysToExpiry >= -3;
        });
        
        if ($expiringAds->count() > 0) {
            // Nájdeme najbližšie expirujúci inzerát (alebo najnedávnejšie expirovaný)
            $nearestExpiring = $expiringAds->sortBy('subscription_expires_at')->first();
            
            // Správny výpočet dní - kladné číslo = zostáva dní, záporné = expiroval pred X dňami
            $daysLeft = now()->diffInDays($nearestExpiring->subscription_expires_at, false);
            
            $subscriptionAlert = [
                'days_left' => (int) $daysLeft, // Celé číslo!
                'expiring_ad' => $nearestExpiring,
                'total_expiring' => $expiringAds->count(),
                'is_expired' => $daysLeft < 0 // True ak už expiroval
            ];
        }

        // Získanie posledných aktivít používateľa
        $recentActivities = $this->getRecentActivities($user);
        
        return view('dashboard', compact('stats', 'subscriptionAlert', 'recentActivities'));
    }

    /**
     * Získa posledné aktivity používateľa
     */
    private function getRecentActivities($user, $limit = 5)
    {
        $activities = collect();

        // 1. Aktivity z Activity modelu
        $userActivities = Activity::where('user_id', $user->id)
            ->latest()
            ->take($limit)
            ->get()
            ->map(function($activity) {
                return [
                    'description' => $activity->description,
                    'time' => $activity->created_at,
                    'type' => $activity->type,
                    'data' => $activity->data
                ];
            });

        $activities = $activities->merge($userActivities);

        // 2. Najnovšie platby
        $recentPayments = AdPayment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($payment) {
                $description = 'Platba za predplatné bola úspešne spracovaná';
                if ($payment->ad) {
                    $description = 'Platba za inzerát "' . ($payment->ad->nickname ?? 'Inzerát #' . $payment->ad->id) . '" bola spracovaná';
                }
                
                return [
                    'description' => $description,
                    'time' => $payment->created_at,
                    'type' => 'payment',
                    'data' => [
                        'amount' => $payment->amount,
                        'currency' => $payment->currency
                    ]
                ];
            });

        $activities = $activities->merge($recentPayments);

        // 3. Najnovšie inzeráty
        $recentAds = Ad::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function($ad) {
                $description = 'Inzerát "' . ($ad->nickname ?? 'Inzerát #' . $ad->id) . '" bol vytvorený';
                
                return [
                    'description' => $description,
                    'time' => $ad->created_at,
                    'type' => 'ad_created',
                    'data' => [
                        'ad_id' => $ad->id,
                        'status' => $ad->status
                    ]
                ];
            });

        $activities = $activities->merge($recentAds);

        // 4. Predplatné ktoré expirovali alebo sa obnovili
        $expiredSubscriptions = Ad::where('user_id', $user->id)
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '>', now()->subDays(7))
            ->latest('subscription_expires_at')
            ->take(2)
            ->get()
            ->map(function($ad) {
                $isExpired = $ad->subscription_expires_at < now();
                
                if ($isExpired) {
                    $description = 'Predplatné pre inzerát "' . ($ad->nickname ?? 'Inzerát #' . $ad->id) . '" expiroval';
                } else {
                    $description = 'Predplatné pre inzerát "' . ($ad->nickname ?? 'Inzerát #' . $ad->id) . '" bolo obnovené';
                }
                
                return [
                    'description' => $description,
                    'time' => $ad->subscription_expires_at,
                    'type' => $isExpired ? 'subscription_expired' : 'subscription_renewed',
                    'data' => [
                        'ad_id' => $ad->id,
                        'expires_at' => $ad->subscription_expires_at
                    ]
                ];
            });

        $activities = $activities->merge($expiredSubscriptions);

        // Zoradenie všetkých aktivít podľa času a obmedzenie na požadovaný počet
        return $activities
            ->sortByDesc('time')
            ->take($limit)
            ->values();
    }
}
