<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class ProcessXmlImagesFromFile extends Command
{
    protected $signature = 'ads:process-xml-images-from-file {--test-mode : Spracuj len prvých 5 inzerátov} {--dry-run : Len ukáž čo by sa spracovalo} {--ad-id= : Spracuj len konkrétny inzerát podľa ID}';
    protected $description = 'Spracuje fotky priamo z XML súboru a priradí k inzerátom';

    private $stats = [
        'processed' => 0,
        'downloaded' => 0,
        'errors' => 0,
        'skipped' => 0
    ];

    public function handle()
    {
        $testMode = $this->option('test-mode');
        $dryRun = $this->option('dry-run');
        $specificAdId = $this->option('ad-id');
        
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

        // Najprv si vytvoríme mapu attachmentov
        $attachments = [];
        foreach ($xml->channel->item as $item) {
            $postType = (string)$item->children('wp', true)->post_type;
            if ($postType === 'attachment') {
                $attachmentId = (int)$item->children('wp', true)->post_id;
                $attachmentUrl = (string)$item->children('wp', true)->attachment_url;
                $attachments[$attachmentId] = $attachmentUrl;
            }
        }

        $this->info('Nájdených ' . count($attachments) . ' attachmentov');

        $processedItems = 0;
        foreach ($xml->channel->item as $item) {
            if ($testMode && $processedItems >= 5) {
                break;
            }
            
            $postType = (string)$item->children('wp', true)->post_type;
            if ($postType !== 'inzeraty') {
                continue;
            }

            // Ak je zadané konkrétne ID, spracuj len ten inzerát
            if ($specificAdId) {
                $wpId = (int)$item->children('wp', true)->post_id;
                $ad = Ad::where('wp_id', $wpId)->first();
                if (!$ad || $ad->id != $specificAdId) {
                    continue;
                }
            }

            $this->processAdImages($item, $attachments, $dryRun);
            $processedItems++;
        }

        $this->displayStats();
        return 0;
    }

    private function processAdImages($item, $attachments, $dryRun = false)
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
            
            $this->info("Spracovávam inzerát: {$title} (ID: {$ad->id}, WP ID: {$wpId})");
            $this->info("Metadáta:");
            $this->info("- Verifikačná fotka: " . ($metaData['i_verifikacna_fotka'] ?? 'nie je'));
            $this->info("- Galéria: " . ($metaData['i_fotografie'] ?? 'nie je'));
            
            $galleryPhotos = [];
            $verificationPhoto = null;

            // Spracuj galériu
            if (!empty($metaData['i_fotografie'])) {
                $photoIds = explode(',', $metaData['i_fotografie']);
                foreach ($photoIds as $photoId) {
                    $photoId = (int)trim($photoId);
                    if (!empty($photoId) && isset($attachments[$photoId])) {
                        $this->info("  -> Spracovávam fotku {$photoId} z URL: {$attachments[$photoId]}");
                        $photoPath = $this->copyPhotoFromUrl($attachments[$photoId], $ad->id, 'gallery', $dryRun);
                        if ($photoPath) {
                            $galleryPhotos[] = $photoPath;
                            $this->info("  -> Uložené ako: {$photoPath}");
                        } else {
                            $this->warn("  -> Nepodarilo sa stiahnuť fotku");
                        }
                    } else {
                        $this->warn("  -> Nenašiel som attachment pre ID: {$photoId}");
                    }
                }
            }

            // Spracuj verifikačnú fotku
            if (!empty($metaData['i_verifikacna_fotka'])) {
                $verificationId = (int)$metaData['i_verifikacna_fotka'];
                if (isset($attachments[$verificationId])) {
                    $this->info("  -> Spracovávam verifikačnú fotku {$verificationId} z URL: {$attachments[$verificationId]}");
                    $verificationPhoto = $this->copyPhotoFromUrl($attachments[$verificationId], $ad->id, 'verification', $dryRun);
                    if ($verificationPhoto) {
                        $this->info("  -> Uložené ako: {$verificationPhoto}");
                    } else {
                        $this->warn("  -> Nepodarilo sa stiahnuť verifikačnú fotku");
                    }
                } else {
                    $this->warn("  -> Nenašiel som attachment pre verifikačnú fotku ID: {$verificationId}");
                }
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
                    $this->info("✅ Aktualizovaný: $title (ID: $wpId) - " . count($galleryPhotos) . " fotiek");
                } else {
                    $this->warn("⚠️ Žiadne fotky na aktualizáciu pre: $title (ID: $wpId)");
                }
            } else {
                $this->line("DRY RUN: $title (ID: $wpId) - " . count($galleryPhotos) . " fotiek galérie" . ($verificationPhoto ? " + verifikačná" : ""));
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->error("Chyba pri spracovaní inzerátu $wpId: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    private function extractMetaData($item)
    {
        $metaData = [];
        foreach ($item->children('wp', true)->postmeta as $meta) {
            $key = (string)$meta->children('wp', true)->meta_key;
            $value = (string)$meta->children('wp', true)->meta_value;
            $metaData[$key] = $value;
        }
        return $metaData;
    }

    private function copyPhotoFromUrl($url, $adId, $type, $dryRun = false)
    {
        $timestamp = time();
        $hash = md5($url . $timestamp);
        $newFilename = "{$timestamp}_{$hash}_{$type}.jpg";
        $localPath = "ads/{$type}/{$newFilename}";
        
        // Skontroluj či súbor už existuje
        if (Storage::disk('public')->exists($localPath)) {
            return "storage/{$localPath}";
        }

        if ($dryRun) {
            return "storage/{$localPath}";
        }

        try {
            // Stiahni súbor z URL
            $imageContent = @file_get_contents($url);
            if ($imageContent === false) {
                $this->error("Nepodarilo sa stiahnuť fotku z URL: {$url}");
                $this->stats['errors']++;
                return null;
            }

            // Ulož súbor
            Storage::disk('public')->put($localPath, $imageContent);
            $this->stats['downloaded']++;
            
            return "storage/{$localPath}";

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->error("Chyba pri sťahovaní fotky z {$url}: " . $e->getMessage());
            return null;
        }
    }

    private function displayStats()
    {
        $this->newLine();
        $this->info('=== ŠTATISTIKY SPRACOVANIA ===');
        $this->info('Spracované inzeráty: ' . $this->stats['processed']);
        $this->info('Skopírované fotky: ' . $this->stats['downloaded']);
        $this->info('Preskočené: ' . $this->stats['skipped']);
        $this->info('Chyby: ' . $this->stats['errors']);
        $this->info('==============================');
    }
} 