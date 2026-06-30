<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect($provider)
    {
        $allowedProviders = ['google', 'facebook'];
        
        if (!in_array($provider, $allowedProviders)) {
            return redirect()->route('login')->with('error', 'Nepodporovaný poskytovateľ prihlásenia.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback
     */
    public function callback($provider)
    {
        try {
            $allowedProviders = ['google', 'facebook'];
            
            if (!in_array($provider, $allowedProviders)) {
                return redirect()->route('login')->with('error', 'Nepodporovaný poskytovateľ prihlásenia.');
            }

            $socialUser = Socialite::driver($provider)->user();
            
            // Normalizujeme email na malé písmená
            $email = strtolower($socialUser->getEmail());
            
            // Skúsime nájsť existujúceho používateľa podľa emailu
            $user = User::where('email', $email)->first();
            
            if ($user) {
                // Ak používateľ existuje, aktualizujeme jeho social ID
                $user->update([
                    $provider . '_id' => $socialUser->getId(),
                ]);
            } else {
                // Vytvoríme nového používateľa
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Používateľ',
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)), // Náhodné heslo
                    $provider . '_id' => $socialUser->getId(),
                ]);
            }

            // Prihlásiť používateľa
            Auth::login($user, true);

            return redirect()->intended(route('dashboard'))->with('success', 'Úspešne ste sa prihlásili cez ' . ucfirst($provider) . '!');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Nastala chyba pri prihlasovaní cez ' . ucfirst($provider) . '. Skúste to znovu.');
        }
    }
} 