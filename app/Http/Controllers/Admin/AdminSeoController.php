<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class AdminSeoController extends Controller
{
    public function index()
    {
        // Načítaj SEO nastavenia prioritne z tabuľky `settings`, fallback na config/seo.php
        $defaultTitle = Setting::get('seo_default_title') ?? config('seo.default_title', config('app.name') . ' - Erotické služby a inzeráty pre dospelých');
        $defaultDescription = Setting::get('seo_default_description') ?? config('seo.default_description', 'Objavte najlepšie erotické služby, tantra masáže a exkluzívne kluby. Bezpečná platforma pre dospelých s overenými inzerátmi.');
        $defaultImageRel = Setting::get('seo_default_image') ?? config('seo.default_image', 'images/uploads/diskretne-dnes-logo-black.png');

        $seoSettings = [
            'default_title' => $defaultTitle,
            'default_description' => $defaultDescription,
            // Na zobrazenie v administrácii použijeme absolútnu URL
            'default_image' => asset($defaultImageRel),
            'sitemap_url' => url('/sitemap.xml'),
            'sitemap_exists' => File::exists(public_path('sitemap.xml')),
        ];
        
        return view('admin.seo.index', compact('seoSettings'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'default_title' => 'required|string|max:60',
            'default_description' => 'required|string|max:160',
            'default_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        // Ulož do DB settings (spoľahlivé aj v produkcii)
        Setting::set('seo_default_title', $request->default_title, 'Predvolený SEO Title');
        Setting::set('seo_default_description', $request->default_description, 'Predvolený SEO Description');

        // Spracovať upload obrázka -> uložiť relatívnu cestu do settings
        $relativeImagePath = Setting::get('seo_default_image') ?? 'storage/images/uploads/diskretne-dnes-logo-black.png';
        if ($request->hasFile('default_image')) {
            $image = $request->file('default_image');
            $imageName = 'seo-default-image.' . $image->getClientOriginalExtension();
            // Uloženie do storage/app/public/images/uploads
            Storage::disk('public')->putFileAs('images/uploads', $image, $imageName);
            // Ukladáme cestu s prefixom storage/ pre asset()
            $relativeImagePath = 'storage/images/uploads/' . $imageName;
            Setting::set('seo_default_image', $relativeImagePath, 'Predvolený SEO obrázok');
        }

        // Okamžite nastav runtime config, aby sa zmeny prejavili hneď
        config([
            'seo.default_title' => $request->default_title,
            'seo.default_description' => $request->default_description,
            'seo.default_image' => $relativeImagePath,
        ]);

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO nastavenia boli úspešne aktualizované.');
    }
    
    public function generateSitemap()
    {
        try {
            $sitemap = $this->buildSitemap();
            
            // Uložiť sitemap do public priečinka
            File::put(public_path('sitemap.xml'), $sitemap);
            
            return redirect()->route('admin.seo.index')
                ->with('success', 'Sitemap bola úspešne vygenerovaná.');
        } catch (\Exception $e) {
            return redirect()->route('admin.seo.index')
                ->with('error', 'Chyba pri generovaní sitemap: ' . $e->getMessage());
        }
    }
    
    private function buildSitemap()
    {
        $urls = collect();
        
        // Základné stránky
        $urls->push([
            'url' => url('/'),
            'lastmod' => now()->toISOString(),
            'priority' => '1.0'
        ]);
        
        $urls->push([
            'url' => route('erotic-clubs'),
            'lastmod' => now()->toISOString(),
            'priority' => '0.8'
        ]);
        
        $urls->push([
            'url' => route('tantra'),
            'lastmod' => now()->toISOString(),
            'priority' => '0.8'
        ]);
        
        $urls->push([
            'url' => route('blog.index'),
            'lastmod' => now()->toISOString(),
            'priority' => '0.7'
        ]);
        
        $urls->push([
            'url' => route('contact'),
            'lastmod' => now()->toISOString(),
            'priority' => '0.6'
        ]);
        
        // Zoznam inzerátov
        $urls->push([
            'url' => route('ads.index'),
            'lastmod' => now()->toISOString(),
            'priority' => '0.8'
        ]);
        
        // Aktívne inzeráty
        \App\Models\Ad::where('status', 'active')
            ->get()
            ->each(function ($ad) use ($urls) {
                $urls->push([
                    'url' => route('ad.show', $ad->id),
                    'lastmod' => $ad->updated_at->toISOString(),
                    'priority' => '0.9'
                ]);
            });
        
        // Blog články (len publikované)
        \App\Models\BlogPost::where('is_published', true)
            ->get()
            ->each(function ($article) use ($urls) {
                $urls->push([
                    'url' => route('blog.show', $article->slug),
                    'lastmod' => $article->updated_at->toISOString(),
                    'priority' => '0.7'
                ]);
            });
        
        // Erotické kluby (jednotlivé kluby)
        \App\Models\EroticClub::get()
            ->each(function ($club) use ($urls) {
                $urls->push([
                    'url' => route('erotic-clubs.show', $club->slug),
                    'lastmod' => $club->updated_at->toISOString(),
                    'priority' => '0.8'
                ]);
            });
        
        // Generovať XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['url']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}