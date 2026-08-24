<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EroticClub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EroticClubController extends Controller
{
    public function index()
    {
        $clubs = EroticClub::orderBy('position')->paginate(10);
        return view('admin-page.kluby.index', compact('clubs'));
    }

    public function create()
    {
        return view('admin-page.kluby.create');
    }

    public function store(Request $request)
    {
        Log::info('Začiatok spracovania požiadavky na vytvorenie klubu');
        Log::info('Prijaté dáta:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^\\+421 ?9[0-9]{2} ?[0-9]{3} ?[0-9]{3}$/'],
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'hours_weekdays' => 'nullable|string|max:255',
            'hours_weekend' => 'nullable|string|max:255',
            'hours_sunday' => 'nullable|string|max:255',
            'services' => 'nullable|array',
            'services.*' => 'string',
            'logo' => 'nullable|image|max:2048',
            'image' => 'required|image|max:2048',
        ]);

        // Pridám prefix +421 pred uložením
        $validated['phone'] = preg_replace('/\s+/', ' ', trim($validated['phone']));

        Log::info('Validácia prešla úspešne');

        try {
            // Ukladáme na disk public (storage/app/public)
            $clubsDir = 'clubs';
            $logosDir = 'clubs/logos';
            
            Log::info('Používam priame public cesty:', [
                'clubs_dir' => $clubsDir,
                'logos_dir' => $logosDir
            ]);
            
            \Storage::disk('public')->makeDirectory($clubsDir);
            \Storage::disk('public')->makeDirectory($logosDir);

            if ($request->hasFile('logo')) {
                Log::info('Logo súbor bol nájdený');
                $logoFile = $request->file('logo');
                
                if (!$logoFile->isValid()) {
                    Log::error('Logo súbor nie je platný');
                    throw new \Exception('Logo súbor nie je platný');
                }

                $logoFileName = time() . '_' . Str::slug($logoFile->getClientOriginalName()) . '.' . $logoFile->getClientOriginalExtension();
                
                Log::info('Ukladám logo na disk public');
                \Storage::disk('public')->putFileAs($logosDir, $logoFile, $logoFileName);
                $validated['logo_path'] = 'storage/clubs/logos/' . $logoFileName;
            }

            if ($request->hasFile('image')) {
                Log::info('Hlavný obrázok bol nájdený');
                $imageFile = $request->file('image');

                if (!$imageFile->isValid()) {
                    Log::error('Obrázok nie je platný');
                    throw new \Exception('Obrázok nie je platný');
                }

                $imageFileName = time() . '_' . Str::slug($imageFile->getClientOriginalName()) . '.' . $imageFile->getClientOriginalExtension();
                
                Log::info('Ukladám obrázok na disk public');
                \Storage::disk('public')->putFileAs($clubsDir, $imageFile, $imageFileName);
                $validated['image_path'] = 'storage/clubs/' . $imageFileName;
            } else {
                throw new \Exception('Hlavný obrázok je povinný');
            }

            if (!isset($validated['services'])) {
                $validated['services'] = [];
            }

            // Zabezpečíme nepovinné pole working_hours pre DB stĺpec bez default hodnoty
            if (!isset($validated['working_hours'])) {
                $validated['working_hours'] = '';
            }

            // Zabezpečíme nepovinné pole pricing pre DB stĺpec bez default hodnoty
            if (!isset($validated['pricing'])) {
                $validated['pricing'] = '';
            }

            $validated['slug'] = Str::slug($validated['name']);
            $validated['position'] = EroticClub::max('position') + 1;

            // Ošetrenie is_active na boolean
            $validated['is_active'] = $request->has('is_active');

            $club = EroticClub::create($validated);
            Log::info('Klub bol úspešne vytvorený:', ['club_id' => $club->id]);

            return redirect()->route('admin.kluby.index')
                ->with('success', 'Klub bol úspešne vytvorený.');

        } catch (\Exception $e) {
            Log::error('Chyba pri vytváraní klubu:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Nastala chyba pri vytváraní klubu: ' . $e->getMessage());
        }
    }

    public function edit(EroticClub $club)
    {
        return view('admin-page.kluby.edit', compact('club'));
    }

    public function update(Request $request, EroticClub $club)
    {
        Log::info('Update metóda začína', [
            'club_id' => $club->id,
            'has_logo' => $request->hasFile('logo'),
            'has_image' => $request->hasFile('image')
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^\\+421 ?9[0-9]{2} ?[0-9]{3} ?[0-9]{3}$/'],
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'hours_weekdays' => 'nullable|string|max:255',
            'hours_weekend' => 'nullable|string|max:255',
            'hours_sunday' => 'nullable|string|max:255',
            'services' => 'nullable|array',
            'services.*' => 'string',
            'logo' => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:2048',
        ]);

        // Pridám prefix +421 pred uložením
        $validated['phone'] = preg_replace('/\s+/', ' ', trim($validated['phone']));

        // Odstránenie hlavného obrázka ak je zaškrtnuté vymazanie
            if ($request->has('remove_image') && $club->image_path) {
            if (!empty($club->image_path)) {
                $removeImagePath = str_replace('storage/', '', $club->image_path);
                \Storage::disk('public')->delete($removeImagePath);
            }
            $validated['image_path'] = null;
            // Aktualizujem objekt, aby sa pri ďalšom mazaní už neskúšal mazať obrázok
            $club->image_path = null;
            $club->save();
        }

        // Uloženie nového obrázka ak je nahraný
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            Log::info('Spracovávam nový obrázok');
            // Vymazanie starého obrázka ak existuje
            if (!empty($club->image_path)) {
                $oldImagePath = str_replace('storage/', '', $club->image_path);
                \Storage::disk('public')->delete($oldImagePath);
            }
            
            try {
                // Ukladáme na disk public (storage/app/public)
                $clubsDir = 'clubs';
                
                Log::info('Používam priamu public cestu pre obrázky: ' . $clubsDir);
                
                \Storage::disk('public')->makeDirectory($clubsDir);
                
                $imageFile = $request->file('image');
                $imageFileName = time() . '_' . Str::slug($imageFile->getClientOriginalName()) . '.' . $imageFile->getClientOriginalExtension();
                
                Log::info('Ukladám obrázok na disk public');
                \Storage::disk('public')->putFileAs($clubsDir, $imageFile, $imageFileName);
                $validated['image_path'] = 'storage/clubs/' . $imageFileName;
            } catch (\Exception $e) {
                Log::error('Chyba pri ukladaní obrázka', ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        // Uloženie nového loga ak je nahrané
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            Log::info('Spracovávam nové logo');
            // Vymazanie starého loga ak existuje
            if (!empty($club->logo_path)) {
                $oldLogoPath = str_replace('storage/', '', $club->logo_path);
                \Storage::disk('public')->delete($oldLogoPath);
            }
            
            try {
                // Ukladáme na disk public (storage/app/public)
                $clubsDir = 'clubs';
                $logosDir = 'clubs/logos';
                
                Log::info('Používam priame public cesty pre logo:', [
                    'clubs_dir' => $clubsDir,
                    'logos_dir' => $logosDir
                ]);
                
                \Storage::disk('public')->makeDirectory($clubsDir);
                \Storage::disk('public')->makeDirectory($logosDir);
                
                $logoFile = $request->file('logo');
                $logoFileName = time() . '_' . Str::slug($logoFile->getClientOriginalName()) . '.' . $logoFile->getClientOriginalExtension();
                
                Log::info('Ukladám logo na disk public');
                \Storage::disk('public')->putFileAs($logosDir, $logoFile, $logoFileName);
                $validated['logo_path'] = 'storage/clubs/logos/' . $logoFileName;
            } catch (\Exception $e) {
                Log::error('Chyba pri ukladaní loga', ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        // Spracovanie services
        if (!isset($validated['services'])) {
            $validated['services'] = [];
        }

        // Spracovanie is_active
        $validated['is_active'] = $request->has('is_active');

        $validated['slug'] = Str::slug($validated['name']);
        
        $club->update($validated);

        Log::info('Klub úspešne aktualizovaný', ['club_id' => $club->id]);

        return redirect()->route('admin.kluby.index')
            ->with('success', 'Klub bol úspešne aktualizovaný.');
    }

    public function destroy(EroticClub $club)
    {
        // Vymazanie loga ak existuje (z disku public)
        if (!empty($club->logo_path)) {
            $relativeLogo = str_starts_with($club->logo_path, 'storage/')
                ? substr($club->logo_path, strlen('storage/'))
                : $club->logo_path;
            \Storage::disk('public')->delete($relativeLogo);
            Log::info('Vymazané logo (public disk): ' . $relativeLogo);
        }
        
        // Vymazanie obrázka ak existuje (z disku public)
        if (!empty($club->image_path)) {
            $relativeImage = str_starts_with($club->image_path, 'storage/')
                ? substr($club->image_path, strlen('storage/'))
                : $club->image_path;
            \Storage::disk('public')->delete($relativeImage);
            Log::info('Vymazaný obrázok (public disk): ' . $relativeImage);
        }

        $club->delete();

        return redirect()->route('admin.kluby.index')
            ->with('success', 'Klub bol úspešne odstránený.');
    }

    public function updatePositions(Request $request)
    {
        $positions = $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'required|integer|exists:erotic_clubs,id'
        ]);

        foreach ($positions['positions'] as $position => $id) {
            EroticClub::where('id', $id)->update(['position' => $position]);
        }

        return response()->json(['message' => 'Pozície boli úspešne aktualizované']);
    }
} 