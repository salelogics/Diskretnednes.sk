<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SimpleXMLElement;

class ExtractImagesFromXml extends Command
{
    protected $signature = 'ads:extract-images-from-xml';
    protected $description = 'Extrahuje obrázky z WordPress XML súboru';

    public function handle()
    {
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

        $this->info('XML načítaný. Extrahujem obrázky...');

        $outputDir = base_path('wp-content/uploads/2024/11');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $stats = [
            'processed' => 0,
            'downloaded' => 0,
            'errors' => 0,
            'skipped' => 0
        ];

        foreach ($xml->channel->item as $item) {
            $postType = (string)$item->children('wp', true)->post_type;
            if ($postType !== 'attachment') {
                continue;
            }

            $attachmentUrl = (string)$item->children('wp', true)->attachment_url;
            $filename = basename($attachmentUrl);
            $filename = explode('?', $filename)[0];

            $outputPath = $outputDir . '/' . $filename;
            
            // Preskočiť ak už súbor existuje
            if (file_exists($outputPath)) {
                $stats['skipped']++;
                continue;
            }

            try {
                $imageContent = file_get_contents($attachmentUrl);
                if ($imageContent === false) {
                    $this->error("Nepodarilo sa stiahnuť obrázok: $attachmentUrl");
                    $stats['errors']++;
                    continue;
                }

                file_put_contents($outputPath, $imageContent);
                $stats['downloaded']++;
                $this->line("✅ Stiahnutý: $filename");

            } catch (\Exception $e) {
                $this->error("Chyba pri sťahovaní $filename: " . $e->getMessage());
                $stats['errors']++;
            }

            $stats['processed']++;
        }

        $this->newLine();
        $this->info('=== ŠTATISTIKY SPRACOVANIA ===');
        $this->info('Spracované: ' . $stats['processed']);
        $this->info('Stiahnuté: ' . $stats['downloaded']);
        $this->info('Preskočené: ' . $stats['skipped']);
        $this->info('Chyby: ' . $stats['errors']);
        $this->info('==============================');

        return 0;
    }
} 