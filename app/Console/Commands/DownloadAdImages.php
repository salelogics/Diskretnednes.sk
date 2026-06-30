<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadAdImages extends Command
{
    protected $signature = 'ads:download-images {--limit=50 : Maximálny počet inzerátov na spracovanie} {--dry-run : Len ukáž čo by sa stiahlo}';
    protected $description = 'Stiahnutie fotiek inzerátov z WordPress';

    private $wordpressUrl = 'https://erotikon.sk';
    private $downloaded = 0;
    private $skipped = 0;
    private $errors = 0;

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info("Spúšťam stiahnutie fotiek inzerátov...");
        $this->info("Limit: {$limit} inzerátov");
        
        if ($dryRun) {
            $this->warn("DRY RUN - žiadne súbory sa nestiahnu");
        }

        // Vytvor storage adresáre
        if (!$dryRun) {
            Storage::disk('public')->makeDirectory('ads/gallery');
            Storage::disk('public')->makeDirectory('ads/verification');
        }

        // Získaj inzeráty s fotkami
        $ads = Ad::whereNotNull('gallery_photos')
            ->orWhereNotNull('verification_photo')
            ->limit($limit)
            ->get();

        $this->info("Našiel som {$ads->count()} inzerátov s fotkami");

        $progressBar = $this->output->createProgressBar($ads->count());
        $progressBar->start();

        foreach ($ads as $ad) {
            $this->processAdImages($ad, $dryRun);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("=== VÝSLEDKY SŤAHOVANIA ===");
        $this->info("Stiahnuté: {$this->downloaded}");
        $this->info("Preskočené: {$this->skipped}");
        $this->info("Chyby: {$this->errors}");

        return 0;
    }

    private function processAdImages(Ad $ad, bool $dryRun): void
    {
        $this->line("\nSpracovávam inzerát: {$ad->nickname} (ID: {$ad->id})");

        // Spracuj verifikačnú fotku
        if (!empty($ad->verification_photo)) {
            $this->downloadImage($ad->verification_photo, 'verification', $ad->id, $dryRun);
        }

        // Spracuj galériu
        if (!empty($ad->gallery_photos) && is_array($ad->gallery_photos)) {
            foreach ($ad->gallery_photos as $index => $photoId) {
                if (!empty($photoId)) {
                    $this->downloadImage($photoId, 'gallery', $ad->id, $dryRun, $index);
                }
            }
        }
    }

    private function downloadImage(string $mediaId, string $type, int $adId, bool $dryRun, ?int $index = null): void
    {
        try {
            // Získaj URL obrázka z WordPress Media API
            $mediaUrl = $this->getMediaUrl($mediaId);
            
            if (!$mediaUrl) {
                $this->warn("  -> Nenašiel som URL pre media ID: {$mediaId}");
                $this->errors++;
                return;
            }

            // Vytvor názov súboru
            $extension = pathinfo(parse_url($mediaUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (empty($extension)) {
                $extension = 'jpg'; // default
            }

            if ($type === 'verification') {
                $filename = "verification_{$adId}_{$mediaId}.{$extension}";
                $path = "ads/verification/{$filename}";
            } else {
                $suffix = $index !== null ? "_{$index}" : '';
                $filename = "gallery_{$adId}_{$mediaId}{$suffix}.{$extension}";
                $path = "ads/gallery/{$filename}";
            }

            if ($dryRun) {
                $this->info("  -> [DRY RUN] Stiahol by sa: {$mediaUrl} -> {$path}");
                $this->downloaded++;
                return;
            }

            // Skontroluj či súbor už existuje
            if (Storage::disk('public')->exists($path)) {
                $this->line("  -> Preskakujem (už existuje): {$filename}");
                $this->skipped++;
                return;
            }

            // Stiahni obrázok
            $response = Http::timeout(30)->withoutVerifying()->get($mediaUrl);

            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());
                $this->info("  -> Stiahnuté: {$filename}");
                $this->downloaded++;

                // Aktualizuj cestu v databáze
                $this->updateAdImagePath($adId, $type, $mediaId, $path, $index);
            } else {
                $this->error("  -> Chyba pri sťahovaní {$mediaUrl}: HTTP {$response->status()}");
                $this->errors++;
            }

        } catch (\Exception $e) {
            $this->error("  -> Chyba pri spracovaní media ID {$mediaId}: " . $e->getMessage());
            $this->errors++;
        }
    }

    private function getMediaUrl(string $mediaId): ?string
    {
        try {
            $response = Http::timeout(10)->withoutVerifying()
                ->get("{$this->wordpressUrl}/wp-json/wp/v2/media/{$mediaId}");

            if ($response->successful()) {
                $media = $response->json();
                return $media['source_url'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function updateAdImagePath(int $adId, string $type, string $mediaId, string $path, ?int $index = null): void
    {
        $ad = Ad::find($adId);
        if (!$ad) return;

        if ($type === 'verification') {
            $ad->verification_photo = $path;
            $ad->save();
        } else {
            // Aktualizuj galériu - nahraď media ID cestou
            $gallery = $ad->gallery_photos;
            if (is_array($gallery)) {
                foreach ($gallery as $i => $photoId) {
                    if ($photoId == $mediaId) {
                        $gallery[$i] = $path;
                        break;
                    }
                }
                $ad->gallery_photos = $gallery;
                $ad->save();
            }
        }
    }
} 