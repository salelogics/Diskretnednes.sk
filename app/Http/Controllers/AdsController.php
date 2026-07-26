<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();
        $ads = $user->ads()->latest()->get();

        // Štatistiky
        $stats = [
            'total_ads' => $ads->count(),
            'active_ads' => $ads->where('status', 'active')->count(),
            'total_views' => $ads->sum('views'),
            'total_clicks' => $ads->sum('clicks'),
            'avg_ctr' => $ads->sum('views') > 0 ? round(($ads->sum('clicks') / $ads->sum('views')) * 100, 2) : 0
        ];

        $classicPackage = \App\Models\PaymentPackage::where('type', 'classic')->where('is_active', true)->first();

        return view('ads.index', compact('ads', 'stats', 'classicPackage'));
    }

    public function create()
    {
        return view('ads.create');
    }

    public function store(Request $request)
    {
        // Debug informácie
        \Log::info('Ads store request started', [
            'user_id' => Auth::id(),
            'has_verification_photo' => $request->hasFile('verification_photo'),
            'has_gallery_photos' => $request->hasFile('gallery_photos'),
            'has_video' => $request->hasFile('video'),
            'request_data' => $request->except(['verification_photo', 'gallery_photos', 'video']),
            'all_files' => array_keys($request->allFiles())
        ]);

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

            \Log::info('Validation passed successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['verification_photo', 'gallery_photos', 'video'])
            ]);
            throw $e;
        }

        // Upload verification photo do storage/app/public/ads/verification
        $verificationPhotoPath = null;
        if ($request->hasFile('verification_photo')) {
            try {
                $file = $request->file('verification_photo');
                $filename = 'verification_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Ulož súbor na disk public
                \Storage::disk('public')->putFileAs('ads/verification', $file, $filename);
                // Do DB ukladáme cestu s prefixom storage/
                $verificationPhotoPath = 'storage/ads/verification/' . $filename;
                
                \Log::info('Verification photo uploaded successfully', [
                    'path' => $verificationPhotoPath,
                    'filename' => $filename
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Verification photo upload failed: ' . $e->getMessage());
                return back()->withErrors(['verification_photo' => 'Chyba pri nahrávaní overovacieho obrázka: ' . $e->getMessage()])->withInput();
            }
        }

        // Upload gallery photos do storage/app/public/ads/gallery
        $galleryPaths = [];
        if ($request->hasFile('gallery_photos')) {
            try {
                foreach ($request->file('gallery_photos') as $file) {
                    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    \Storage::disk('public')->putFileAs('ads/gallery', $file, $filename);
                    $galleryPaths[] = 'storage/ads/gallery/' . $filename;
                }
                
                \Log::info('Gallery photos uploaded', [
                    'count' => count($galleryPaths),
                    'paths' => $galleryPaths
                ]);
            } catch (\Exception $e) {
                \Log::error('Gallery photos upload failed: ' . $e->getMessage());
                return back()->withErrors(['gallery_photos' => 'Chyba pri nahrávaní galérie obrázkov: ' . $e->getMessage()])->withInput();
            }
        }

        // Upload video pomocou Laravel Storage (public disk)
        $videoPath = null;
        if ($request->hasFile('video')) {
            try {
                $file = $request->file('video');
                $filename = 'video_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                \Storage::disk('public')->putFileAs('ads/videos', $file, $filename);
                $videoPath = 'storage/ads/videos/' . $filename;
                
                \Log::info('Video uploaded successfully', [
                    'path' => $videoPath,
                    'filename' => $filename
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Video upload failed: ' . $e->getMessage());
                return back()->withErrors(['video' => 'Chyba pri nahrávaní videa: ' . $e->getMessage()])->withInput();
            }
        }

        // Príprava dát pre vytvorenie inzerátu
        $adData = [
            'user_id' => Auth::id(),
            'nickname' => $request->nickname,
            'ad_type' => $request->ad_type,
            'nationality' => $request->nationality,
            'age' => $request->age,
            'city' => $request->city,
            'street' => $request->street,
            'offer_type' => $request->offer_type,
            'phone' => $request->phone,
            'contact_methods' => $request->contact_methods ?? [],
            'hours' => $request->hours ?? [],
            'practices' => $request->practices ?? [],
            'description' => $request->description,
            'verification_photo' => $verificationPhotoPath,
            'gallery_photos' => $galleryPaths,
            'video' => $videoPath,
            'height' => $request->height,
            'weight' => $request->weight,
            'breast_size' => $request->breast_size,
            'eye_color' => $request->eye_color,
            'hair_color' => $request->hair_color,
            'tattoos' => $request->tattoos,
            'piercing' => $request->piercing,
            'orientation' => $request->orientation,
            'status' => 'draft', // Nové inzeráty začínajú ako koncept
        ];

        \Log::info('Attempting to create ad with data', [
            'data_keys' => array_keys($adData),
            'user_id' => $adData['user_id'],
            'nickname' => $adData['nickname']
        ]);

        // Vytvorenie inzerátu
        try {
            $ad = Ad::create($adData);

            \Log::info('Ad created successfully', [
                'ad_id' => $ad->id,
                'user_id' => $ad->user_id,
                'nickname' => $ad->nickname
            ]);

            // Poslať email a vytvoriť notifikácie
            try {
                $this->notificationService->adCreated($ad);
                \Log::info('Notifications sent successfully for ad', ['ad_id' => $ad->id]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send notifications for ad', [
                    'ad_id' => $ad->id,
                    'error' => $e->getMessage()
                ]);
                // Pokračujeme aj keď notifikácie zlyhajú
            }

            return redirect()->route('ads.index')->with('success', 'Inzerát bol úspešne vytvorený! ID: AD-' . $ad->id);

        } catch (\Exception $e) {
            \Log::error('Ad creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $adData
            ]);
            
            // Vymazanie nahraných súborov pri chybe
            if ($verificationPhotoPath && Storage::disk('public')->exists($verificationPhotoPath)) {
                Storage::disk('public')->delete($verificationPhotoPath);
            }
            foreach ($galleryPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            if ($videoPath && Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }
            
            return back()->withErrors(['general' => 'Chyba pri vytváraní inzerátu: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        // Admin môže upravovať ľubovoľný inzerát, používateľ iba svoj
        if (Auth::user()->is_admin) {
            $ad = Ad::findOrFail($id);
        } else {
            $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
        }
        
        return view('ads.edit', compact('ad'));
    }

    public function update(Request $request, $id)
    {
        // Admin môže upravovať ľubovoľný inzerát, používateľ iba svoj
        if (Auth::user()->is_admin) {
            $ad = Ad::findOrFail($id);
        } else {
            $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
        }
        
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
            'phone' => 'required|string|max:20|regex:/^[0-9]+$/',
            'contact_methods' => 'nullable|array',
            'hours' => 'nullable|array',
            'practices' => 'nullable|array',
            'description' => 'required|string|min:50',
            'verification_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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

        $updateData = [
            'nickname' => $request->nickname,
            'ad_type' => $request->ad_type,
            'nationality' => $request->nationality,
            'age' => $request->age,
            'city' => $request->city,
            'street' => $request->street,
            'offer_type' => $request->offer_type,
            'phone' => $request->phone,
            'contact_methods' => $request->contact_methods ?? [],
            'hours' => $request->hours ?? [],
            'practices' => $request->practices ?? [],
            'description' => $request->description,
            'height' => $request->height,
            'weight' => $request->weight,
            'breast_size' => $request->breast_size,
            'eye_color' => $request->eye_color,
            'hair_color' => $request->hair_color,
            'tattoos' => $request->tattoos,
            'piercing' => $request->piercing,
            'orientation' => $request->orientation,
        ];

        // Vymazanie verifikačnej fotky ak je požadované
        if ($request->has('remove_verification_photo')) {
            if ($ad->verification_photo) {
                $this->deletePhotoFile($ad->verification_photo, 'verification');
            }
            $updateData['verification_photo'] = null;
        }

        // Upload nového verification photo ak je poskytnuté
        if ($request->hasFile('verification_photo')) {
            // Vymazanie starého súboru ak sa nahráva nový
            if ($ad->verification_photo) {
                $this->deletePhotoFile($ad->verification_photo, 'verification');
            }
            
            $file = $request->file('verification_photo');
            $filename = 'verification_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ulož súbor na disk public
            \Storage::disk('public')->makeDirectory('ads/verification');
            \Storage::disk('public')->putFileAs('ads/verification', $file, $filename);
            $updateData['verification_photo'] = 'storage/ads/verification/' . $filename;
        }

        // Poznámka: Vymazávanie jednotlivých fotiek sa teraz rieši cez AJAX endpoint deleteGalleryPhoto()

        // Upload nových gallery photos ak sú poskytnuté
        if ($request->hasFile('gallery_photos')) {
            $currentGallery = isset($updateData['gallery_photos']) ? $updateData['gallery_photos'] : ($ad->gallery_photos ?? []);
            
            foreach ($request->file('gallery_photos') as $file) {
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                \Storage::disk('public')->makeDirectory('ads/gallery');
                \Storage::disk('public')->putFileAs('ads/gallery', $file, $filename);
                $currentGallery[] = 'storage/ads/gallery/' . $filename;
            }
            $updateData['gallery_photos'] = $currentGallery;
        }

        // Vymazanie videa ak je požadované
        if ($request->has('remove_video')) {
            if ($ad->video) {
                $videoStoragePath = str_replace('storage/', '', $ad->video);
                if (Storage::disk('public')->exists($videoStoragePath)) {
                    Storage::disk('public')->delete($videoStoragePath);
                }
            }
            $updateData['video'] = null;
        }

        // Upload nového videa ak je poskytnuté
        if ($request->hasFile('video')) {
            // Vymazanie starého súboru ak sa nahráva nové
            if ($ad->video) {
                $oldVideoStoragePath = str_replace('storage/', '', $ad->video);
                if (Storage::disk('public')->exists($oldVideoStoragePath)) {
                    Storage::disk('public')->delete($oldVideoStoragePath);
                }
            }
            
            $file = $request->file('video');
            $filename = 'video_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('ads/videos', $file, $filename);
            $updateData['video'] = 'storage/ads/videos/' . $filename;
        }

        $ad->update($updateData);

        return redirect()->route('ads.index')->with('success', 'Inzerát bol úspešne aktualizovaný.');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
            
            // Ak je inzerát v stave draft, nie je možné ho aktivovať priamo
            if ($ad->status === 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Inzerát v stave "Koncept" nie je možné aktivovať. Najskôr si objednajte predplatné.'
                ], 422);
            }
            
            // Ak chce aktivovať, kontrolujeme či má aktívne predplatné
            if ($ad->status === 'inactive') {
                if (!$ad->isSubscriptionActive()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pre aktiváciu inzerátu potrebujete aktívne predplatné.'
                    ], 422);
                }
            }
            
            $newStatus = $ad->status === 'active' ? 'inactive' : 'active';
            $ad->update(['status' => $newStatus]);
            
            $message = $newStatus === 'active' ? 'Inzerát bol aktivovaný.' : 'Inzerát bol deaktivovaný.';
            
            // Vždy vráť JSON response pre AJAX
            return response()->json([
                'success' => true,
                'message' => $message,
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri aktualizácii inzerátu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function renewSubscription($id)
    {
        $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
        
        // Predĺženie predplatného o 30 dní
        $expiresAt = now()->addDays(30);
        
        $ad->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => $expiresAt
        ]);
        
        return redirect()->route('ads.index')->with('success', 'Predplatné bolo obnovené do ' . $expiresAt->format('d.m.Y'));
    }

    public function destroy(Request $request, $id)
    {
        // Admin môže vymazať ľubovoľný inzerát, používateľ iba svoj
        if (Auth::user()->is_admin) {
            $ad = Ad::findOrFail($id);
        } else {
            $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
        }
        
        // Vymazanie verification photo - kontrola oboch možných lokácií
        if ($ad->verification_photo) {
            $this->deletePhotoFile($ad->verification_photo, 'verification');
        }
        
        // Vymazanie gallery photos - kontrola oboch možných lokácií
        if ($ad->gallery_photos && is_array($ad->gallery_photos)) {
            foreach ($ad->gallery_photos as $photo) {
                $this->deletePhotoFile($photo, 'gallery');
            }
        }
        
        // Vymazanie videa
        if ($ad->video) {
            $videoStoragePath = str_replace('storage/', '', $ad->video);
            if (Storage::disk('public')->exists($videoStoragePath)) {
                Storage::disk('public')->delete($videoStoragePath);
            }
        }
        
        $ad->delete();
        
        // AJAX požiadavka
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inzerát bol úspešne vymazaný.'
            ]);
        }
        
        return redirect()->route('ads.index')->with('success', 'Inzerát bol úspešne vymazaný.');
    }
    
    /**
     * Helper metóda pre mazanie fotiek z public/images/uploads
     */
    private function deletePhotoFile(string $photoPath, string $type): void
    {
        try {
            // Ak je to len filename (staré inzeráty), skús mazať z public/images/uploads/
            if (!str_contains($photoPath, '/')) {
                $publicPath = public_path("images/uploads/ads/{$type}/{$photoPath}");
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                    \Log::info("Deleted photo from public path: {$publicPath}");
                    return;
                }
            }
            
            // Ak už obsahuje images/uploads/, použij priamo
            if (str_starts_with($photoPath, 'images/uploads/')) {
                $storagePath = 'storage/' . ltrim($photoPath, '/');
                $diskPath = str_replace('storage/', '', $storagePath);
                if (Storage::disk('public')->exists($diskPath)) {
                    Storage::disk('public')->delete($diskPath);
                    \Log::info("Deleted photo from storage path (converted from images/uploads): {$diskPath}");
                    return;
                }
                $publicPath = public_path($storagePath);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                    \Log::info("Deleted photo from public path: {$publicPath}");
                    return;
                }
            }

            // Ak obsahuje storage/images/uploads/, použij storage disk
            if (str_starts_with($photoPath, 'storage/images/uploads/')) {
                $diskPath = str_replace('storage/', '', $photoPath);
                if (Storage::disk('public')->exists($diskPath)) {
                    Storage::disk('public')->delete($diskPath);
                    \Log::info("Deleted photo from storage path: {$diskPath}");
                    return;
                }
                $publicPath = public_path($photoPath);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                    \Log::info("Deleted photo from public path: {$publicPath}");
                    return;
                }
            }
            
            // Skús mazať zo Storage API (staré inzeráty)
            $diskCandidate = str_replace('storage/', '', $photoPath);
            if (Storage::disk('public')->exists($diskCandidate)) {
                Storage::disk('public')->delete($diskCandidate);
                \Log::info("Deleted photo from storage: {$photoPath}");
                return;
            }
            
            // Pre backward compatibility - skús rôzne možné cesty
            $possiblePaths = [
                $photoPath,
                "ads/{$type}/" . basename($photoPath),
                basename($photoPath)
            ];
            
            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    \Log::info("Deleted photo from storage (fallback): {$path}");
                    return;
                }
                
                // Skús aj public path
                $publicPath = public_path("images/uploads/ads/{$type}/" . basename($path));
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                    \Log::info("Deleted photo from public path (fallback): {$publicPath}");
                    return;
                }
            }
            
            \Log::warning("Could not find photo file to delete: {$photoPath}");
            
        } catch (\Exception $e) {
            \Log::error("Error deleting photo file: {$photoPath}", [
                'error' => $e->getMessage(),
                'type' => $type
            ]);
        }
    }

    public function statistics($id)
    {
        $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
        
        // Reálne štatistiky pre konkrétny inzerát
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Pre teraz použijeme celkové štatistiky, neskôr môžeme pridať daily tracking
        $stats = [
            'views_today' => $this->getDailyViews($ad, $today),
            'views_week' => $this->getWeeklyViews($ad, $thisWeek),
            'views_month' => $ad->views,
            'clicks_today' => $this->getDailyClicks($ad, $today),
            'clicks_week' => $this->getWeeklyClicks($ad, $thisWeek),
            'clicks_month' => $ad->clicks,
            'ctr' => round($ad->ctr, 2)
        ];
        
        return view('ads.statistics', compact('ad', 'stats'));
    }
    
    private function getDailyViews($ad, $date)
    {
        // Ak nemáme daily tracking, použijeme aproximáciu z celkových zobrazení
        // Celkové zobrazenia / počet dní od vytvorenia * factor pre aktuálnosť
        $daysSinceCreated = $ad->created_at->diffInDays(now()) ?: 1;
        $avgDaily = $ad->views / $daysSinceCreated;
        
        // Ak je inzerát mladší ako týždeň, zobrazíme vyššie čísla
        if ($daysSinceCreated <= 7) {
            return (int) max(0, $avgDaily * 1.5);
        }
        
        return (int) max(0, $avgDaily);
    }
    
    private function getWeeklyViews($ad, $weekStart)
    {
        // Aproximácia týždenných zobrazení
        $daysSinceCreated = $ad->created_at->diffInDays(now()) ?: 1;
        $avgWeekly = ($ad->views / $daysSinceCreated) * 7;
        
        return (int) max(0, $avgWeekly);
    }
    
    private function getDailyClicks($ad, $date)
    {
        // Rovnaký princíp ako pre zobrazenia
        $daysSinceCreated = $ad->created_at->diffInDays(now()) ?: 1;
        $avgDaily = $ad->clicks / $daysSinceCreated;
        
        if ($daysSinceCreated <= 7) {
            return (int) max(0, $avgDaily * 1.5);
        }
        
        return (int) max(0, $avgDaily);
    }
    
    private function getWeeklyClicks($ad, $weekStart)
    {
        // Aproximácia týždenných klikov
        $daysSinceCreated = $ad->created_at->diffInDays(now()) ?: 1;
        $avgWeekly = ($ad->clicks / $daysSinceCreated) * 7;
        
        return (int) max(0, $avgWeekly);
    }

    public function deleteGalleryPhoto($id, $index)
    {
        $ad = Ad::where('user_id', Auth::id())->findOrFail($id);
        
        $currentGallery = $ad->gallery_photos ?? [];
        
        // Skontrolovať, či index existuje
        if (!isset($currentGallery[$index])) {
            return response()->json(['success' => false, 'message' => 'Fotka nebola nájdená.'], 404);
        }
        
        // Vymazať súbor z disku
        $photoPath = $currentGallery[$index];
        $this->deletePhotoFile($photoPath, 'gallery');
        
        // Odstrániť z array
        $newGallery = [];
        foreach ($currentGallery as $i => $path) {
            if ($i != $index) {
                $newGallery[] = $path;
            }
        }
        
        // Aktualizovať v databáze
        $ad->update(['gallery_photos' => $newGallery]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Fotka bola úspešne vymazaná.',
            'remaining_count' => count($newGallery)
        ]);
    }
} 