<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaSyncFromPublic extends Command
{
    protected $signature = 'media:sync-from-public {--overwrite : Overwrite destination files if they already exist} {--dry-run : Only report, do not copy files} {--summary : Print only summary (bez per-súbor logov)} {--force : Spustiť aj na produkcii}';

    protected $description = 'Skopíruje všetky súbory z public/images/uploads do storage/app/public/images/uploads (disk public) rekurzívne, bezpečne a idempotentne';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $overwrite = (bool) $this->option('overwrite');
        $summaryOnly = (bool) $this->option('summary');
        $force = (bool) $this->option('force');

        // Na produkcii defaultne nevykonávame – zabráni timeoutom v deployi
        if (app()->environment('production') && !$force) {
            $this->warn('media:sync-from-public je na produkcii vypnutý. Spusti s --force ak to naozaj chceš.');
            return self::SUCCESS;
        }

        $sourceRoot = public_path('images/uploads');
        $this->info('Zdroj: ' . $sourceRoot);
        $this->info('Cieľ (disk public): storage/images/uploads');
        if ($dryRun) {
            $this->warn('DRY RUN: súbory sa nebudú kopírovať');
        }

        if (!File::exists($sourceRoot)) {
            $this->error('Zdrojový priečinok neexistuje: ' . $sourceRoot);
            return self::FAILURE;
        }

        $total = 0;
        $copied = 0;
        $skipped = 0;

        // Prejdi rekurzívne všetky súbory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceRoot, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $total++;

            // Relatívna cesta od images/uploads
            $relative = ltrim(str_replace($sourceRoot, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            // Normalizuj na forward slashes
            $relative = str_replace('\\\\', '/', $relative);

            $destRelative = 'images/uploads/' . $relative; // cesta na disk public
            $destPublicPath = public_path('storage/' . $destRelative);

            if (!$overwrite && File::exists($destPublicPath)) {
                $skipped++;
                if (!$summaryOnly) {
                    $this->line("  Preskakujem (existuje): storage/{$destRelative}");
                }
                continue;
            }

            if (!$summaryOnly) {
                $this->line("  Kopírujem: {$file->getPathname()} -> storage/{$destRelative}");
            }

            if (!$dryRun) {
                File::ensureDirectoryExists(dirname($destPublicPath), 0755, true);
                $bytes = Storage::disk('public')->put($destRelative, File::get($file->getPathname()));
                if ($bytes === false) {
                    $this->error('    NEPODARILO SA zapísať: storage/' . $destRelative);
                } else {
                    $copied++;
                }
            }
        }

        $this->line('');
        $this->info("Súhrn: celkom={$total}, skopírované={$copied}, preskočené={$skipped}");
        $this->info('Hotovo.');

        return self::SUCCESS;
    }
}


