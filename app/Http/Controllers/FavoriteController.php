<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Ad;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        // Získam obľúbené inzeráty pre aktuálneho používateľa/session
        $query = Favorite::with('ad');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $favorites = $query->get();
        $ads = $favorites->map(fn($favorite) => $favorite->ad)->filter();

        return view('favorites.index', compact('ads'));
    }

    public function toggle(Request $request, Ad $ad)
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        \Log::info('Favorite toggle attempt', [
            'ad_id' => $ad->id,
            'user_id' => $userId,
            'session_id' => $sessionId ? substr($sessionId, 0, 10) . '...' : null
        ]);

        // Kontrola či už existuje v obľúbených
        $query = Favorite::where('ad_id', $ad->id);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $favorite = $query->first();

        if ($favorite) {
            // Odobrať z obľúbených
            $favorite->delete();
            $isFavorite = false;
            \Log::info('Favorite removed', ['favorite_id' => $favorite->id]);
        } else {
            // Pridať do obľúbených
            $newFavorite = Favorite::create([
                'session_id' => $userId ? null : $sessionId,
                'user_id' => $userId,
                'ad_id' => $ad->id
            ]);
            $isFavorite = true;
            \Log::info('Favorite added', ['favorite_id' => $newFavorite->id]);
        }

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json(['is_favorite' => $isFavorite]);
        }

        return back()->with('success', $isFavorite ? 'Inzerát pridaný do obľúbených' : 'Inzerát odobraný z obľúbených');
    }

    public function check(Request $request, Ad $ad)
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        $query = Favorite::where('ad_id', $ad->id);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $isFavorite = $query->exists();

        return response()->json(['is_favorite' => $isFavorite]);
    }

    public function count(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        $query = Favorite::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}
