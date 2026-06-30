<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\BlogPost;
use App\Models\EroticClub;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaMigrateToStorage extends Command
{
    protected $signature = 'media:migrate-to-storage {--dry-run : Only report actions, do not modify files or database} {--limit=0 : Process at most N records of each type (0 = all)} {--summary : Print only summary (bez per-záznam logov)} {--force : Spustiť aj na produkcii}';

    protected $description = 'Move legacy media from public/images to storage/app/public and update DB paths to storage/...';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $summaryOnly = (bool) $this->option('summary');
        $force = (bool) $this->option('force');

        // Na produkcii defaultne nevykonávame – zabráni timeoutom v deployi
        if (app()->environment('production') && !$force) {
            $this->warn('media:migrate-to-storage je na produkcii vypnutý. Spusti s --force ak to naozaj chceš.');
            return self::SUCCESS;
        }

        $this->info('Starting media migration to storage/app/public');
        if ($dryRun) {
            $this->warn('DRY RUN enabled - no changes will be written');
        }

        $this->line('');
        $this->migrateAds($dryRun, $limit, $summaryOnly);
        $this->line('');
        $this->migrateBlogPosts($dryRun, $limit, $summaryOnly);
        $this->line('');
        $this->migrateClubs($dryRun, $limit, $summaryOnly);
        $this->line('');
        $this->migrateSeoSetting($dryRun);

        $this->line('');
        $this->info('Done. Ensure public/storage symlink exists: php artisan storage:link');
        return self::SUCCESS;
    }

    private function migrateAds(bool $dryRun, int $limit, bool $summaryOnly): void
    {
        $this->info('Migrating Ads media...');
        $query = Ad::query();
        if ($limit > 0) {
            $query->limit($limit);
        }

        $movedFiles = 0;
        $updatedRows = 0;

        $query->orderBy('id')->chunkById(200, function ($ads) use (&$movedFiles, &$updatedRows, $dryRun) {
            foreach ($ads as $ad) {
                $rowChanged = false;

                // Verification photo
                if (is_string($ad->verification_photo) && $ad->verification_photo !== '' && !$this->isExternalUrl($ad->verification_photo)) {
                    $newPath = $this->migrateSingleImage(
                        $ad->verification_photo,
                        'images/uploads/ads/verification',
                        $dryRun,
                        $movedFiles
                    );
                    if ($newPath && $newPath !== $ad->verification_photo) {
                        $ad->verification_photo = $newPath;
                        $rowChanged = true;
                        if (!$summaryOnly) {
                            $this->line("  AD #{$ad->id} verification -> {$newPath}");
                        }
                    }
                }

                // Gallery photos
                $gallery = $ad->gallery_photos;
                if (is_array($gallery) && count($gallery) > 0) {
                    $newGallery = [];
                    $changed = false;
                    foreach ($gallery as $photo) {
                        if (!is_string($photo) || $photo === '' || $this->isExternalUrl($photo)) {
                            $newGallery[] = $photo;
                            continue;
                        }
                        $new = $this->migrateSingleImage(
                            $photo,
                            'images/uploads/ads/gallery',
                            $dryRun,
                            $movedFiles
                        );
                        $newGallery[] = $new ?: $photo;
                        if ($new && $new !== $photo) {
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        $ad->gallery_photos = $newGallery;
                        $rowChanged = true;
                        if (!$summaryOnly) {
                            $this->line("  AD #{$ad->id} gallery updated");
                        }
                    }
                }

                // Video
                if (is_string($ad->video) && $ad->video !== '' && !$this->isExternalUrl($ad->video)) {
                    $newVideo = $this->migrateSingleFile(
                        $ad->video,
                        'ads/videos',
                        $dryRun,
                        $movedFiles
                    );
                    if ($newVideo && $newVideo !== $ad->video) {
                        $ad->video = $newVideo;
                        $rowChanged = true;
                        if (!$summaryOnly) {
                            $this->line("  AD #{$ad->id} video -> {$newVideo}");
                        }
                    }
                }

                if ($rowChanged && !$dryRun) {
                    $ad->save();
                    $updatedRows++;
                }
            }
        });

        $this->info("Ads: moved files {$movedFiles}, updated rows {$updatedRows}");
    }

    private function migrateBlogPosts(bool $dryRun, int $limit, bool $summaryOnly): void
    {
        $this->info('Migrating BlogPost images...');
        $query = BlogPost::query();
        if ($limit > 0) {
            $query->limit($limit);
        }

        $movedFiles = 0;
        $updatedRows = 0;

        $query->orderBy('id')->chunkById(200, function ($posts) use (&$movedFiles, &$updatedRows, $dryRun) {
            foreach ($posts as $post) {
                $path = $post->image_path;
                if (!$path || $this->isExternalUrl($path)) {
                    continue;
                }

                $newPath = $this->migrateSingleImage(
                    $path,
                    $this->detectBlogTargetDir($path),
                    $dryRun,
                    $movedFiles
                );

                if ($newPath && $newPath !== $path) {
                    if (!$dryRun) {
                        $post->image_path = $newPath;
                        $post->save();
                    }
                    $updatedRows++;
                    if (!$summaryOnly) {
                        $this->line("  BlogPost #{$post->id} -> {$newPath}");
                    }
                }
            }
        });

        $this->info("BlogPosts: moved files {$movedFiles}, updated rows {$updatedRows}");
    }

    private function migrateClubs(bool $dryRun, int $limit, bool $summaryOnly): void
    {
        $this->info('Migrating EroticClub images...');
        $query = EroticClub::query();
        if ($limit > 0) {
            $query->limit($limit);
        }

        $movedFiles = 0;
        $updatedRows = 0;

        $query->orderBy('id')->chunkById(200, function ($clubs) use (&$movedFiles, &$updatedRows, $dryRun) {
            foreach ($clubs as $club) {
                $rowChanged = false;

                // Logo
                if ($club->logo_path && !$this->isExternalUrl($club->logo_path)) {
                    $newLogo = $this->migrateSingleImage(
                        $club->logo_path,
                        'images/uploads/clubs/logos',
                        $dryRun,
                        $movedFiles
                    );
                    if ($newLogo && $newLogo !== $club->logo_path) {
                        $club->logo_path = $newLogo;
                        $rowChanged = true;
                        if (!$summaryOnly) {
                            $this->line("  Club #{$club->id} logo -> {$newLogo}");
                        }
                    }
                }

                // Main image
                if ($club->image_path && !$this->isExternalUrl($club->image_path)) {
                    $newImg = $this->migrateSingleImage(
                        $club->image_path,
                        'images/uploads/clubs',
                        $dryRun,
                        $movedFiles
                    );
                    if ($newImg && $newImg !== $club->image_path) {
                        $club->image_path = $newImg;
                        $rowChanged = true;
                        if (!$summaryOnly) {
                            $this->line("  Club #{$club->id} image -> {$newImg}");
                        }
                    }
                }

                if ($rowChanged && !$dryRun) {
                    $club->save();
                    $updatedRows++;
                }
            }
        });

        $this->info("Clubs: moved files {$movedFiles}, updated rows {$updatedRows}");
    }

    private function migrateSeoSetting(bool $dryRun): void
    {
        $this->info('Migrating SEO default image setting (if needed)...');
        $key = 'seo_default_image';
        $current = Setting::get($key);
        if (!$current || $this->isExternalUrl($current)) {
            $this->line('  No change needed.');
            return;
        }

        $dummy = 0;
        $newPath = $this->migrateSingleImage($current, 'images/uploads', $dryRun, $dummy);
        if ($newPath && $newPath !== $current) {
            $this->line("  Setting {$key} -> {$newPath}");
            if (!$dryRun) {
                Setting::set($key, $newPath, 'Predvolený SEO obrázok');
            }
        } else {
            $this->line('  No change needed.');
        }
    }

    private function migrateSingleImage(string $dbPath, string $targetDirOnDisk, bool $dryRun, int &$movedFiles): ?string
    {
        // Resolve source absolute path candidates
        $basename = basename($dbPath);
        $candidates = [];

        if (str_starts_with($dbPath, 'storage/')) {
            $candidates[] = public_path($dbPath);
        }
        if (str_starts_with($dbPath, 'images/')) {
            $candidates[] = public_path($dbPath);
        }
        if (str_starts_with($dbPath, 'ads/')) {
            // Pokryť rôzne legacy umiestnenia
            $candidates[] = public_path('images/uploads/' . $dbPath); // images/uploads/ads/...
            $candidates[] = public_path('storage/' . $dbPath); // storage/ads/...
            $candidates[] = public_path('images/' . $dbPath); // images/ads/...
        }
        // Bare filename
        $candidates[] = public_path('images/uploads/ads/verification/' . $basename);
        $candidates[] = public_path('images/uploads/ads/gallery/' . $basename);
        // Niektoré staré inzeráty mali súbory priamo v images/uploads/ads/ bez podpriečinkov
        $candidates[] = public_path('images/uploads/ads/' . $basename);
        $candidates[] = public_path('images/blog/' . $basename);
        $candidates[] = public_path('images/blog/images/' . $basename);
        // Niektoré staršie uploady blogu v images/uploads/
        $candidates[] = public_path('images/uploads/' . $basename);
        $candidates[] = public_path('images/uploads/clubs/' . $basename);
        $candidates[] = public_path('images/uploads/clubs/logos/' . $basename);

        $source = $this->firstExisting($candidates);
        if (!$source) {
            // Try if already at target under public/storage
            $maybeExisting = public_path('storage/' . trim($targetDirOnDisk, '/') . '/' . $basename);
            if (File::exists($maybeExisting)) {
                return 'storage/' . trim($targetDirOnDisk, '/') . '/' . $basename;
            }
            // Extra fallback: súbory pod storage/ads alebo storage/ads/{verification|gallery}
            if (str_starts_with($targetDirOnDisk, 'images/uploads/ads/')) {
                $legacyCandidates = [
                    public_path('storage/ads/' . $basename),
                    public_path('storage/ads/verification/' . $basename),
                    public_path('storage/ads/gallery/' . $basename),
                ];
                foreach ($legacyCandidates as $legacyPath) {
                    if (File::exists($legacyPath)) {
                        if (!$dryRun) {
                            File::ensureDirectoryExists(dirname($maybeExisting), 0755, true);
                            Storage::disk('public')->put(trim($targetDirOnDisk, '/') . '/' . $basename, File::get($legacyPath));
                            // BEZ mazania legacy súboru – bezpečný copy
                        }
                        return 'storage/' . trim($targetDirOnDisk, '/') . '/' . $basename;
                    }
                }
            }
            return null;
        }

        $targetRelativeDiskPath = trim($targetDirOnDisk, '/') . '/' . $basename; // relative on public disk
        $targetPublicPath = public_path('storage/' . $targetRelativeDiskPath);

        if (File::exists($targetPublicPath)) {
            return 'storage/' . $targetRelativeDiskPath;
        }

        $this->line('  Move: ' . $source . ' -> ' . $targetPublicPath);

        if (!$dryRun) {
            File::ensureDirectoryExists(dirname($targetPublicPath), 0755, true);
            $bytes = Storage::disk('public')->put($targetRelativeDiskPath, File::get($source));
            if ($bytes === false) {
                $this->error('    Failed to write target');
                return null;
            }
            // Nemažeme zdroj – bezpečný copy, nie move
        }

        $movedFiles++;
        return 'storage/' . $targetRelativeDiskPath;
    }

    private function migrateSingleFile(string $dbPath, string $targetDirOnDisk, bool $dryRun, int &$movedFiles): ?string
    {
        $basename = basename($dbPath);
        $candidates = [];

        if (str_starts_with($dbPath, 'storage/')) {
            $candidates[] = public_path($dbPath);
        }
        if (str_starts_with($dbPath, 'images/')) {
            $candidates[] = public_path($dbPath);
        }
        if (str_starts_with($dbPath, 'ads/')) {
            $candidates[] = public_path('images/uploads/' . $dbPath);
            $candidates[] = public_path('storage/' . $dbPath);
        }
        $candidates[] = public_path('images/uploads/ads/videos/' . $basename);

        $source = $this->firstExisting($candidates);
        if (!$source) {
            $maybeExisting = public_path('storage/' . trim($targetDirOnDisk, '/') . '/' . $basename);
            if (File::exists($maybeExisting)) {
                return 'storage/' . trim($targetDirOnDisk, '/') . '/' . $basename;
            }
            return null;
        }

        $targetRelativeDiskPath = trim($targetDirOnDisk, '/') . '/' . $basename;
        $targetPublicPath = public_path('storage/' . $targetRelativeDiskPath);

        if (File::exists($targetPublicPath)) {
            return 'storage/' . $targetRelativeDiskPath;
        }

        $this->line('  Move: ' . $source . ' -> ' . $targetPublicPath);

        if (!$dryRun) {
            File::ensureDirectoryExists(dirname($targetPublicPath), 0755, true);
            $bytes = Storage::disk('public')->put($targetRelativeDiskPath, File::get($source));
            if ($bytes === false) {
                $this->error('    Failed to write target');
                return null;
            }
            @File::delete($source);
        }

        $movedFiles++;
        return 'storage/' . $targetRelativeDiskPath;
    }

    private function detectBlogTargetDir(string $dbPath): string
    {
        if (str_contains($dbPath, '/images/')) {
            return 'images/blog/images';
        }
        return 'images/blog';
    }

    private function firstExisting(array $candidates): ?string
    {
        foreach ($candidates as $path) {
            if ($path && File::exists($path) && File::isFile($path)) {
                return $path;
            }
        }
        return null;
    }

    private function isExternalUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}


