<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAdsController extends Controller
{
    public function index()
    {
        $ads = Ad::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_ads' => Ad::count(),
            'active_ads' => Ad::where('status', 'active')->count(),
            'pending_ads' => Ad::where('status', 'pending')->count(),
            'inactive_ads' => Ad::where('status', 'inactive')->count(),
        ];

        return view('admin.inzeraty.index', compact('ads', 'stats'));
    }

    /**
     * AJAX vyhľadávanie inzerátov
     */
    public function search(Request $request)
    {
        try {
            $searchQuery = $request->input('search', '');
            
            $query = Ad::with(['user']);
            
            if (!empty($searchQuery)) {
                $query->where(function($q) use ($searchQuery) {
                    // Vyhľadávanie v ID inzerátu
                    $q->where('id', 'like', '%' . $searchQuery . '%')
                      // Vyhľadávanie v nickname
                      ->orWhere('nickname', 'like', '%' . $searchQuery . '%')
                      // Vyhľadávanie v opise
                      ->orWhere('description', 'like', '%' . $searchQuery . '%')
                      // Vyhľadávanie v type ponuky
                      ->orWhere('offer_type', 'like', '%' . $searchQuery . '%')
                      // Vyhľadávanie v stave
                      ->orWhere('status', 'like', '%' . $searchQuery . '%')
                      // Vyhľadávanie v meste
                      ->orWhere('city', 'like', '%' . $searchQuery . '%')
                      // Vyhľadávanie v používateľskych údajoch
                      ->orWhereHas('user', function($userQuery) use ($searchQuery) {
                          $userQuery->where('name', 'like', '%' . $searchQuery . '%')
                                   ->orWhere('email', 'like', '%' . $searchQuery . '%');
                      });
                });
            }
            
            $ads = $query->orderBy('created_at', 'desc')->paginate(20);
            $count = $ads->total();
            
            // Generovanie HTML pre tabuľku
            $html = '';
            if ($ads->count() > 0) {
                foreach ($ads as $ad) {
                    $html .= view('admin.inzeraty.partials.table-row', compact('ad'))->render();
                }
            } else {
                $html = view('admin.inzeraty.partials.no-results')->render();
            }
            
            // Generovanie HTML pre pagináciu
            $pagination = $ads->hasPages() ? $ads->links()->render() : '';
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $count,
                'pagination' => $pagination
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Admin ads search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri vyhľadávaní inzerátov'
            ], 500);
        }
    }

    public function show(Ad $ad)
    {
        $ad->load(['user', 'payments']);
        return view('admin.inzeraty.show', compact('ad'));
    }

    public function updateStatus(Request $request, Ad $ad)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending,rejected'
        ]);

        $ad->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Stav inzerátu bol úspešne aktualizovaný.');
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('admin.inzeraty.index')->with('success', 'Inzerát bol úspešne vymazaný.');
    }

    /**
     * Predplatenie inzerátu zadarmo (len pre admin)
     */
    public function subscribeForFree(Request $request, Ad $ad)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:payment_packages,id'
            ]);

            $package = \App\Models\PaymentPackage::active()->findOrFail($request->package_id);

            // Vytvorenie "platby" so statusom completed (zadarmo)
            $payment = \App\Models\AdPayment::create([
                'user_id' => $ad->user_id,
                'ad_id' => $ad->id,
                'payment_package_id' => $package->id,
                'payment_id' => 'ADMIN-' . strtoupper(\Illuminate\Support\Str::random(10)),
                'amount' => 0, // Zadarmo
                'currency' => 'EUR',
                'payment_method' => 'admin_free',
                'status' => 'completed',
                'duration_days' => $package->duration_days,
                'is_featured' => $package->is_featured,
                'is_top_ad' => $package->is_top_ad,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'package_name' => $package->name,
                    'package_type' => $package->type,
                    'created_by_admin' => true,
                    'admin_user_id' => auth()->id(),
                    'admin_note' => 'Predplatené zadarmo administrátorom'
                ]
            ]);

            // Aktivovať predplatné
            $payment->markAsCompleted();

            return response()->json([
                'success' => true,
                'message' => 'Inzerát bol úspešne predplatený zadarmo!',
                'payment_id' => $payment->payment_id,
                'package_name' => $package->name
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin free subscription error: ' . $e->getMessage(), [
                'ad_id' => $ad->id,
                'package_id' => $request->package_id,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri predplatení: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zobrazenie zoznamu platieb za inzeráty
     */
    public function payments()
    {
        $payments = \App\Models\AdPayment::with(['user', 'ad', 'paymentPackage'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => \App\Models\AdPayment::count(),
            'pending' => \App\Models\AdPayment::where('status', 'pending')->count(),
            'completed' => \App\Models\AdPayment::where('status', 'completed')->count(),
            'failed' => \App\Models\AdPayment::where('status', 'failed')->count(),
            'pending_bank_transfers' => \App\Models\AdPayment::where('status', 'pending')
                ->where('payment_method', 'bank_transfer')->count(),
        ];

        return view('admin.inzeraty.payments', compact('payments', 'stats'));
    }

    /**
     * Schválenie platby administrátorom (hlavne pre bankové prevody)
     */
    public function approvePayment(\App\Models\AdPayment $payment)
    {
        try {
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Platba už bola spracovaná'
                ], 400);
            }

            // Automaticky sa aktivujú len SMS a Stripe platby
            // Bankové prevody musia byť schválené administrátorom
            if ($payment->payment_method === 'bank_transfer') {
                \Log::info('Admin approving bank transfer payment', [
                    'payment_id' => $payment->payment_id,
                    'admin_id' => auth()->id(),
                    'amount' => $payment->amount
                ]);
            }

            DB::transaction(function () use ($payment) {
                $payment->markAsCompleted();
                $payment->generateInvoiceNumber();
                
                // Logujeme kto platbu potvrdil
                $payment->update([
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'approved_by_admin' => auth()->id(),
                        'approved_at' => now()->toISOString(),
                        'approval_note' => 'Platba potvrdená administrátorom'
                    ])
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Platba bola úspešne potvrdená a inzerát aktivovaný!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error approving payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri potvrdzovaní platby: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zamietnutie platby administrátorom
     */
    public function rejectPayment(\App\Models\AdPayment $payment)
    {
        try {
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Platba už bola spracovaná'
                ], 400);
            }

            $payment->update([
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], [
                    'rejected_by_admin' => auth()->id(),
                    'rejected_at' => now()->toISOString(),
                    'rejection_note' => 'Platba zamietnutá administrátorom'
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Platba bola zamietnutá'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error rejecting payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri zamietaní platby: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zobrazenie formulára pre vytvorenie inzerátu (pre admina)
     */
    public function create()
    {
        return view('admin.inzeraty.create');
    }

    /**
     * Uloženie nového inzerátu (pre admina)
     */
    public function store(Request $request)
    {
        // Použijeme existujúcu logiku z AdsController
        $adsController = new \App\Http\Controllers\AdsController(app(\App\Services\NotificationService::class));
        
        // Najprv overíme že je prihlásený admin
        if (!auth()->user()->is_admin) {
            return redirect()->route('admin.inzeraty.index')
                ->withErrors(['general' => 'Nemáte oprávnenie na túto akciu.']);
        }

        // Pre admin môžu byť niektoré validácie menej prísne
        // ale v princípe používame rovnakú logiku
        try {
            // Validácia
            $request->validate([
                'nickname' => 'required|string|max:255',
                'ad_type' => 'required|in:zena,muz,trans,par,klub',
                'nationality' => 'required|string|max:255',
                'age' => 'required|integer|min:18|max:99',
                'city' => 'required|string|max:255',
                'street' => 'nullable|string|max:255',
                'offer_type' => 'required|array|min:1',
                'offer_type.*' => 'string|max:255',
                'girl_selection' => 'required|string|max:255',
                'experience' => 'required|string|max:255',
                'phone' => 'required|string|max:20|regex:/^[0-9]+$/',
                'contact_methods' => 'nullable|array',
                'hours' => 'nullable|array',
                'practices' => 'nullable|array',
                'description' => 'required|string|min:50',
                'verification_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'gallery_photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'video' => 'nullable|mimes:mp4,avi,mov,wmv|max:51200',
                'height' => 'nullable|integer|min:140|max:220',
                'weight' => 'nullable|integer|min:40|max:150',
                'breast_size' => 'nullable|string|max:10',
                'eye_color' => 'nullable|string|max:255',
                'hair_color' => 'nullable|string|max:255',
                'tattoos' => 'nullable|string|max:255',
                'piercing' => 'nullable|string|max:255',
                'orientation' => 'nullable|string|max:255',
            ]);

            // Vytvoríme inzerát s admin flagom
            $result = $this->createAdWithAdminPrivileges($request);
            
            if ($result['success']) {
                return redirect()->route('admin.inzeraty.index')
                    ->with('success', 'Inzerát bol úspešne vytvorený! ID: AD-' . $result['ad']->id);
            } else {
                return back()->withErrors(['general' => $result['message']])->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Admin ad creation failed: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Chyba pri vytváraní inzerátu: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Vytvorenie inzerátu s admin privilégiami
     */
    private function createAdWithAdminPrivileges(Request $request)
    {
        try {
            // Upload verification photo (storage/app/public)
            $verificationPhotoPath = null;
            if ($request->hasFile('verification_photo')) {
                $file = $request->file('verification_photo');
                $filename = 'verification_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                \Storage::disk('public')->putFileAs('ads/verification', $file, $filename);
                $verificationPhotoPath = 'storage/ads/verification/' . $filename;
            }

            // Upload gallery photos (storage/app/public)
            $galleryPaths = [];
            if ($request->hasFile('gallery_photos')) {
                foreach ($request->file('gallery_photos') as $file) {
                    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    \Storage::disk('public')->putFileAs('ads/gallery', $file, $filename);
                    $galleryPaths[] = 'storage/ads/gallery/' . $filename;
                }
            }

            // Upload video (storage/app/public)
            $videoPath = null;
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $filename = 'video_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                \Storage::disk('public')->putFileAs('ads/videos', $file, $filename);
                $videoPath = 'storage/ads/videos/' . $filename;
            }

            // Príprava dát pre inzerát
            $adData = [
                'user_id' => auth()->id(), // Admin bude vlastníkom
                'nickname' => $request->nickname,
                'ad_type' => $request->ad_type,
                'nationality' => $request->nationality,
                'age' => $request->age,
                'city' => $request->city,
                'street' => $request->street,
                'offer_type' => json_encode($request->offer_type),
                'girl_selection' => $request->girl_selection,
                'experience' => $request->experience,
                'phone' => $request->phone,
                'contact_methods' => json_encode($request->contact_methods ?? []),
                'hours' => json_encode($request->hours ?? []),
                'practices' => json_encode($request->practices ?? []),
                'description' => $request->description,
                'verification_photo' => $verificationPhotoPath,
                'gallery_photos' => json_encode($galleryPaths),
                'video' => $videoPath,
                'height' => $request->height,
                'weight' => $request->weight,
                'breast_size' => $request->breast_size,
                'eye_color' => $request->eye_color,
                'hair_color' => $request->hair_color,
                'tattoos' => $request->tattoos,
                'piercing' => $request->piercing,
                'orientation' => $request->orientation,
                'status' => 'active', // Admin inzeráty sú automaticky aktívne
            ];

            // Vytvorenie inzerátu
            $ad = Ad::create($adData);

            \Log::info('Admin created ad successfully', [
                'ad_id' => $ad->id,
                'admin_id' => auth()->id(),
                'nickname' => $ad->nickname
            ]);

            return [
                'success' => true,
                'ad' => $ad
            ];

        } catch (\Exception $e) {
            \Log::error('Admin ad creation failed: ' . $e->getMessage());
            
            // Cleanup uploaded files on error
            if (isset($verificationPhotoPath) && file_exists(public_path($verificationPhotoPath))) {
                unlink(public_path($verificationPhotoPath));
            }
            if (isset($galleryPaths)) {
                foreach ($galleryPaths as $path) {
                    if (file_exists(public_path($path))) {
                        unlink(public_path($path));
                    }
                }
            }
            if (isset($videoPath) && file_exists(public_path($videoPath))) {
                unlink(public_path($videoPath));
            }

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Admin predĺženie inzerátu zadarmo
     */
    public function extendAd(Request $request, Ad $ad)
    {
        try {
            // Validácia vstupu
            $request->validate([
                'package_type' => 'required|in:classic,premium',
                'days' => 'required|integer|in:7,30'
            ]);

            $packageType = $request->package_type;
            $days = $request->days;

            // Vytvoríme "admin" platbu s 0 cenou
            $adPayment = AdPayment::create([
                'ad_id' => $ad->id,
                'user_id' => $ad->user_id,
                'payment_package_id' => null, // Admin payment nemá package_id
                'amount' => 0.00, // Admin platba je zadarmo
                'currency' => 'EUR',
                'payment_method' => 'admin_free',
                'status' => 'completed',
                'duration_days' => $days,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addDays($days),
                'is_featured' => $packageType === 'premium',
                'is_top_ad' => $packageType === 'premium',
                'gateway_payment_id' => 'admin_' . time(),
                'gateway_response' => json_encode([
                    'admin_created' => true,
                    'admin_user_id' => auth()->id(),
                    'admin_name' => auth()->user()->name,
                    'package_type' => $packageType,
                    'package_days' => $days,
                    'notes' => "Admin predĺženie ({$packageType} {$days} dní)"
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'admin_extension' => true,
                    'original_request' => $request->all()
                ])
            ]);

            // Nastavíme nový dátum expirácie
            $expiresAt = now()->addDays($days);
            
            // Aktualizujeme inzerát
            $ad->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => $expiresAt,
                'status' => 'active', // Admin predĺženie automaticky aktivuje inzerát
                'featured' => $packageType === 'premium' ? true : false,
                'top_ad' => $packageType === 'premium' ? true : false,
            ]);

            \Log::info('Admin extended ad successfully', [
                'ad_id' => $ad->id,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'package_type' => $packageType,
                'days' => $days,
                'expires_at' => $expiresAt,
                'payment_id' => $adPayment->id
            ]);

            return response()->json([
                'success' => true,
                'message' => "Inzerát bol úspešne predĺžený o {$days} dní ({$packageType})",
                'data' => [
                    'ad_id' => $ad->id,
                    'expires_at' => $expiresAt->format('d.m.Y H:i'),
                    'package_type' => $packageType,
                    'days' => $days,
                    'payment_id' => $adPayment->id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Neplatné údaje: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Admin extend ad failed', [
                'ad_id' => $ad->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri predĺžení inzerátu: ' . $e->getMessage()
            ], 500);
        }
    }
} 