<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\BlogPost;
use App\Models\EroticClub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MediaPruneMissing extends Command
{
    protected $signature = 'media:prune-missing {--fix : Prepíše DB a odstráni neexistujúce cesty} {--limit=0 : Spracuj najviac N záznamov každého typu (0 = všetko)}';

    protected $description = 'Vyčistí databázu od odkazov na neexistujúce súbory (verification, gallery, blog, clubs). Najprv spusti bez --fix (dry-run).';

    public function handle(): int
    {
        $doFix = (bool) $this->option('fix');
        $limit = (int) $this->option('limit');

        $this->info('Pruning missing media references' . ($doFix ? ' (WRITE MODE)' : ' (DRY RUN)'));
        $this->line('');

        $ads = $this->pruneAds($doFix, $limit);
        $posts = $this->pruneBlog($doFix, $limit);
        $clubs = $this->pruneClubs($doFix, $limit);

        $this->line('');
        $this->info("Summary: ads={$ads['changed']}/{$ads['scanned']} changed, posts={$posts['changed']}/{$posts['scanned']} changed, clubs={$clubs['changed']}/{$clubs['scanned']} changed");

        return self::SUCCESS;
    }

    private function fileExists(string $relativePublicPath): bool
    {
        return File::exists(public_path($relativePublicPath));
    }

    private function pruneAds(bool $doFix, int $limit): array
    {
        $this->info('ADS');
        $changed = 0;
        $scanned = 0;

        $query = Ad::query()->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $query->chunkById(200, function ($ads) use (&$changed, &$scanned, $doFix) {
            foreach ($ads as $ad) {
                $scanned++;
                $rowChanged = false;

                // verification_photo
                $p = $ad->verification_photo;
                if (is_string($p) && $p !== '') {
                    if (!$this->fileExists(ltrim($this->normalizePath($p), '/'))) {
                        $this->line("  AD #{$ad->id} remove verification: {$p}");
                        if ($doFix) {
                            $ad->verification_photo = null;
                            $rowChanged = true;
                        }
                    }
                }

                // gallery_photos
                $gallery = $ad->gallery_photos;
                if (is_array($gallery) && count($gallery) > 0) {
                    $newGallery = [];
                    foreach ($gallery as $g) {
                        if (is_string($g) && $g !== '' && $this->fileExists(ltrim($this->normalizePath($g), '/'))) {
                            $newGallery[] = $g;
                        } else {
                            $this->line("  AD #{$ad->id} prune gallery: {$g}");
                        }
                    }
                    if ($doFix && count($newGallery) !== count($gallery)) {
                        $ad->gallery_photos = $newGallery;
                        $rowChanged = true;
                    }
                }

                if ($rowChanged && $doFix) {
                    $ad->save();
                    $changed++;
                }
            }
        });

        $this->info("  scanned={$scanned}, changed={$changed}");
        return compact('changed', 'scanned');
    }

    private function pruneBlog(bool $doFix, int $limit): array
    {
        $this->info('BLOG POSTS');
        $changed = 0;
        $scanned = 0;

        $query = BlogPost::query()->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $query->chunkById(200, function ($posts) use (&$changed, &$scanned, $doFix) {
            foreach ($posts as $post) {
                $scanned++;
                $p = $post->image_path;
                if (is_string($p) && $p !== '') {
                    if (!$this->fileExists(ltrim($this->normalizePath($p), '/'))) {
                        $this->line("  POST #{$post->id} remove image: {$p}");
                        if ($doFix) {
                            $post->image_path = null;
                            $post->save();
                            $changed++;
                        }
                    }
                }
            }
        });

        $this->info("  scanned={$scanned}, changed={$changed}");
        return compact('changed', 'scanned');
    }

    private function pruneClubs(bool $doFix, int $limit): array
    {
        $this->info('EROTIC CLUBS');
        $changed = 0;
        $scanned = 0;

        $query = EroticClub::query()->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $query->chunkById(200, function ($clubs) use (&$changed, &$scanned, $doFix) {
            foreach ($clubs as $club) {
                $scanned++;
                $rowChanged = false;

                foreach (['logo_path', 'image_path'] as $field) {
                    $p = $club->{$field};
                    if (is_string($p) && $p !== '' && !$this->fileExists(ltrim($this->normalizePath($p), '/'))) {
                        $this->line("  CLUB #{$club->id} remove {$field}: {$p}");
                        if ($doFix) {
                            $club->{$field} = null;
                            $rowChanged = true;
                        }
                    }
                }

                if ($rowChanged && $doFix) {
                    $club->save();
                    $changed++;
                }
            }
        });

        $this->info("  scanned={$scanned}, changed={$changed}");
        return compact('changed', 'scanned');
    }

    private function normalizePath(string $path): string
    {
        // Ak začína na storage/, necháme tak
        if (str_starts_with($path, 'storage/')) {
            return $path;
        }
        // Legacy images/... necháme tak
        if (str_starts_with($path, 'images/')) {
            return $path;
        }
        // Niektoré staré záznamy mohli mať len názov
        if (!str_contains($path, '/')) {
            return 'images/uploads/ads/gallery/' . $path;
        }
        return $path;
    }
}


