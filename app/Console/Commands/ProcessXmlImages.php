<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class ProcessXmlImages extends Command
{
    protected $signature = 'ads:process-xml-images {--dry-run : Len ukáž čo by sa spracovalo} {--test-mode : Spracuj len prvých 5 inzerátov}';
    protected $description = 'Spracuje fotky z XML súboru - stiahne ich z WordPress a priradí k inzerátom';

    private $stats = [
        'processed' => 0,
        'downloaded' => 0,
        'errors' => 0,
        'skipped' => 0
    ];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $testMode = $this->option('test-mode');
        
        if ($dryRun) {
            $this->warn('DRY RUN - žiadne súbory sa nestiahnu');
        }
        
        if ($testMode) {
            $this->warn('TEST MODE - len prvých 5 inzerátov');
        }

        $xmlFile = base_path('erotikon.WordPress.2025-07-07.xml');
        
        if (!file_exists($xmlFile)) {
            $this->error("XML súbor sa nenašiel: $xmlFile");
            return 1;
        }

        $this->info('Načítavam XML súbor...');
        
        try {
            $xml = simplexml_load_file($xmlFile);
            if (!$xml) {
                $this->error('Nepodarilo sa načítať XML súbor');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Chyba pri načítaní XML: ' . $e->getMessage());
            return 1;
        }

        $this->info('XML načítaný. Spracovávam inzeráty s fotkami...');

        $items = $xml->channel->item;
        $processedItems = 0;
        
        foreach ($items as $item) {
            if ($testMode && $processedItems >= 5) {
                break;
            }
            
            $postType = (string)$item->children('wp', true)->post_type;
            if ($postType !== 'inzeraty') {
                continue;
            }

            $this->processAdImages($item, $dryRun);
            $processedItems++;
        }

        $this->displayStats();
        return 0;
    }

    private function processAdImages($item, $dryRun = false)
    {
        try {
            $wpId = (int)$item->children('wp', true)->post_id;
            $title = (string)$item->title;
            
            // Nájdi inzerát v databáze
            $ad = Ad::where('wp_id', $wpId)->first();
            if (!$ad) {
                $this->stats['skipped']++;
                return;
            }

            $this->stats['processed']++;

            // Extrahuj metadata
            $metaData = $this->extractMetaData($item);
            
            $galleryPhotos = [];
            $verificationPhoto = null;

            // Spracuj galériu
            if (!empty($metaData['i_fotografie'])) {
                $photoIds = explode(',', $metaData['i_fotografie']);
                foreach ($photoIds as $photoId) {
                    $photoId = trim($photoId);
                    if (!empty($photoId)) {
                        $photoPath = $this->downloadPhoto($photoId, $ad->id, 'gallery', $dryRun);
                        if ($photoPath) {
                            $galleryPhotos[] = $photoPath;
                        }
                    }
                }
            }

            // Spracuj verifikačnú fotku
            if (!empty($metaData['i_verifikacna_fotka'])) {
                $verificationId = $metaData['i_verifikacna_fotka'];
                $verificationPhoto = $this->downloadPhoto($verificationId, $ad->id, 'verification', $dryRun);
            }

            // Aktualizuj databázu
            if (!$dryRun) {
                $updateData = [];
                if (!empty($galleryPhotos)) {
                    $updateData['gallery_photos'] = $galleryPhotos;
                }
                if ($verificationPhoto) {
                    $updateData['verification_photo'] = $verificationPhoto;
                }
                
                if (!empty($updateData)) {
                    $ad->update($updateData);
                    $this->line("✅ Aktualizovaný: $title (ID: $wpId) - " . count($galleryPhotos) . " fotiek");
                }
            } else {
                $this->line("DRY RUN: $title (ID: $wpId) - " . count($galleryPhotos) . " fotiek galérie" . ($verificationPhoto ? " + verifikačná" : ""));
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->error("Chyba pri spracovaní inzerátu $wpId: " . $e->getMessage());
        }
    }

    private function downloadPhoto($photoId, $adId, $type, $dryRun = false)
    {
        $filename = "{$type}_{$photoId}.jpg";
        $localPath = "ads/{$filename}";
        
        // Skontroluj či súbor už existuje
        if (Storage::disk('public')->exists($localPath)) {
            return "storage/{$localPath}";
        }

        if ($dryRun) {
            return "storage/{$localPath}";
        }

        // Stiahni zo WordPress API
        $wpApiUrl = "https://diskretnednes.sk/wp-json/wp/v2/media/{$photoId}";
        
        try {
            $response = Http::withoutVerifying()->timeout(30)->get($wpApiUrl);
            
            if (!$response->successful()) {
                $this->stats['errors']++;
                return null;
            }

            $mediaData = $response->json();
            $imageUrl = $mediaData['source_url'] ?? null;
            
            if (!$imageUrl) {
                $this->stats['errors']++;
                return null;
            }

            // Stiahni obrázok
            $imageResponse = Http::withoutVerifying()->timeout(60)->get($imageUrl);
            
            if (!$imageResponse->successful()) {
                $this->stats['errors']++;
                return null;
            }

            // Ulož súbor
            Storage::disk('public')->put($localPath, $imageResponse->body());
            $this->stats['downloaded']++;
            
            return "storage/{$localPath}";

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->error("Chyba pri sťahovaní fotky $photoId: " . $e->getMessage());
            return null;
        }
    }

    private function extractMetaData($item)
    {
        $metaData = [];
        
        foreach ($item->children('wp', true)->postmeta as $meta) {
            $key = (string)$meta->meta_key;
            $value = (string)$meta->meta_value;
            $metaData[$key] = $value;
        }
        
        return $metaData;
    }

    private function displayStats()
    {
        $this->info('');
        $this->info('=== ŠTATISTIKY SPRACOVANIA ===');
        $this->info("Spracované inzeráty: {$this->stats['processed']}");
        $this->info("Stiahnuté fotky: {$this->stats['downloaded']}");
        $this->info("Preskočené: {$this->stats['skipped']}");
        $this->info("Chyby: {$this->stats['errors']}");
        $this->info('==============================');
    }
} 