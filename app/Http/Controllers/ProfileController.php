<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Normalizujeme email na malé písmená
        if (isset($validated['email'])) {
            $validated['email'] = strtolower($validated['email']);
        }
        
        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's extended profile information.
     */
    public function updateExtended(Request $request): RedirectResponse
    {
        $user = $request->user();
        
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
                return Redirect::route('profile.edit')->withErrors(['photo' => 'Chyba pri nahrávaní obrázka.']);
            }
        }

        $user->update($data);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
