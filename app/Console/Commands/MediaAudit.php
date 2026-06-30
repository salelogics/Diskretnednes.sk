<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\BlogPost;
use App\Models\EroticClub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MediaAudit extends Command
{
    protected $signature = 'media:audit {--samples=5 : Koľko príkladov vypísať na kategóriu}';

    protected $description = 'Skontroluje DB cesty médií (legacy images/... vs storage/...) a chýbajúce fyzické súbory.';

    public function handle(): int
    {
        $sampleLimit = (int) $this->option('samples');

        $this->info('Auditing media paths and files...');
        $this->line('');

        $this->auditAds($sampleLimit);
        $this->line('');
        $this->auditBlogPosts($sampleLimit);
        $this->line('');
        $this->auditClubs($sampleLimit);

        $this->line('');
        $this->info('Audit hotový.');
        return self::SUCCESS;
    }

    private function isExternal(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    private function fileExistsForPublicPath(string $relativePublicPath): bool
    {
        return File::exists(public_path($relativePublicPath));
    }

    private function auditAds(int $sampleLimit): void
    {
        $this->info('ADS');

        $legacyVerification = 0;
        $storageVerification = 0;
        $missingVerification = 0;
        $legacyGallery = 0;
        $storageGallery = 0;
        $missingGallery = 0;

        $legacyVerificationSamples = [];
        $missingVerificationSamples = [];
        $legacyGallerySamples = [];
        $missingGallerySamples = [];

        Ad::orderBy('id')->chunkById(300, function ($ads) use (
            &$legacyVerification,
            &$storageVerification,
            &$missingVerification,
            &$legacyGallery,
            &$storageGallery,
            &$missingGallery,
            &$legacyVerificationSamples,
            &$missingVerificationSamples,
            &$legacyGallerySamples,
            &$missingGallerySamples,
            $sampleLimit
        ) {
            foreach ($ads as $ad) {
                // verification_photo
                $p = $ad->verification_photo;
                if (is_string($p) && $p !== '' && !$this->isExternal($p)) {
                    if (str_starts_with($p, 'images/')) {
                        $legacyVerification++;
                        if (count($legacyVerificationSamples) < $sampleLimit) {
                            $legacyVerificationSamples[] = [
                                'ad_id' => $ad->id,
                                'path' => $p,
                            ];
                        }
                        if (!$this->fileExistsForPublicPath($p)) {
                            $missingVerification++;
                            if (count($missingVerificationSamples) < $sampleLimit) {
                                $missingVerificationSamples[] = [
                                    'ad_id' => $ad->id,
                                    'expected' => $p,
                                ];
                            }
                        }
                    } elseif (str_starts_with($p, 'storage/')) {
                        $storageVerification++;
                        if (!$this->fileExistsForPublicPath($p)) {
                            $missingVerification++;
                            if (count($missingVerificationSamples) < $sampleLimit) {
                                $missingVerificationSamples[] = [
                                    'ad_id' => $ad->id,
                                    'expected' => $p,
                                ];
                            }
                        }
                    }
                }

                // gallery_photos
                $gallery = $ad->gallery_photos;
                if (is_array($gallery)) {
                    foreach ($gallery as $g) {
                        if (!is_string($g) || $g === '' || $this->isExternal($g)) {
                            continue;
                        }
                        if (str_starts_with($g, 'images/')) {
                            $legacyGallery++;
                            if (count($legacyGallerySamples) < $sampleLimit) {
                                $legacyGallerySamples[] = [
                                    'ad_id' => $ad->id,
                                    'path' => $g,
                                ];
                            }
                            if (!$this->fileExistsForPublicPath($g)) {
                                $missingGallery++;
                                if (count($missingGallerySamples) < $sampleLimit) {
                                    $missingGallerySamples[] = [
                                        'ad_id' => $ad->id,
                                        'expected' => $g,
                                    ];
                                }
                            }
                        } elseif (str_starts_with($g, 'storage/')) {
                            $storageGallery++;
                            if (!$this->fileExistsForPublicPath($g)) {
                                $missingGallery++;
                                if (count($missingGallerySamples) < $sampleLimit) {
                                    $missingGallerySamples[] = [
                                        'ad_id' => $ad->id,
                                        'expected' => $g,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->line(" - verification_photo: legacy={$legacyVerification}, storage={$storageVerification}, missing={$missingVerification}");
        $this->line(" - gallery_photos: legacy={$legacyGallery}, storage={$storageGallery}, missing={$missingGallery}");

        if (!empty($legacyVerificationSamples)) {
            $this->line('   Príklady legacy verification:');
            foreach ($legacyVerificationSamples as $s) {
                $this->line('    - AD #' . $s['ad_id'] . ' -> ' . $s['path']);
            }
        }
        if (!empty($missingVerificationSamples)) {
            $this->line('   Príklady chýbajúcich verification:');
            foreach ($missingVerificationSamples as $s) {
                $this->line('    - AD #' . $s['ad_id'] . ' -> ' . $s['expected']);
            }
        }
        if (!empty($legacyGallerySamples)) {
            $this->line('   Príklady legacy gallery:');
            foreach ($legacyGallerySamples as $s) {
                $this->line('    - AD #' . $s['ad_id'] . ' -> ' . $s['path']);
            }
        }
        if (!empty($missingGallerySamples)) {
            $this->line('   Príklady chýbajúcich gallery:');
            foreach ($missingGallerySamples as $s) {
                $this->line('    - AD #' . $s['ad_id'] . ' -> ' . $s['expected']);
            }
        }
    }

    private function auditBlogPosts(int $sampleLimit): void
    {
        $this->info('BLOG POSTS');

        $legacy = 0;
        $storage = 0;
        $missing = 0;
        $legacySamples = [];
        $missingSamples = [];

        BlogPost::orderBy('id')->chunkById(300, function ($posts) use (
            &$legacy, &$storage, &$missing, &$legacySamples, &$missingSamples, $sampleLimit
        ) {
            foreach ($posts as $post) {
                $p = $post->image_path;
                if (!is_string($p) || $p === '' || $this->isExternal($p)) {
                    continue;
                }
                if (str_starts_with($p, 'images/')) {
                    $legacy++;
                    if (count($legacySamples) < $sampleLimit) {
                        $legacySamples[] = ['post_id' => $post->id, 'path' => $p];
                    }
                    if (!$this->fileExistsForPublicPath($p)) {
                        $missing++;
                        if (count($missingSamples) < $sampleLimit) {
                            $missingSamples[] = ['post_id' => $post->id, 'expected' => $p];
                        }
                    }
                } elseif (str_starts_with($p, 'storage/')) {
                    $storage++;
                    if (!$this->fileExistsForPublicPath($p)) {
                        $missing++;
                        if (count($missingSamples) < $sampleLimit) {
                            $missingSamples[] = ['post_id' => $post->id, 'expected' => $p];
                        }
                    }
                }
            }
        });

        $this->line(" - image_path: legacy={$legacy}, storage={$storage}, missing={$missing}");

        if (!empty($legacySamples)) {
            $this->line('   Príklady legacy:');
            foreach ($legacySamples as $s) {
                $this->line('    - Post #' . $s['post_id'] . ' -> ' . $s['path']);
            }
        }
        if (!empty($missingSamples)) {
            $this->line('   Príklady chýbajúcich:');
            foreach ($missingSamples as $s) {
                $this->line('    - Post #' . $s['post_id'] . ' -> ' . $s['expected']);
            }
        }
    }

    private function auditClubs(int $sampleLimit): void
    {
        $this->info('EROTIC CLUBS');

        $fields = ['logo_path', 'image_path'];
        foreach ($fields as $field) {
            $legacy = 0;
            $storage = 0;
            $missing = 0;
            $legacySamples = [];
            $missingSamples = [];

            EroticClub::orderBy('id')->chunkById(300, function ($clubs) use (&$legacy, &$storage, &$missing, &$legacySamples, &$missingSamples, $sampleLimit, $field) {
                foreach ($clubs as $club) {
                    $p = $club->{$field};
                    if (!is_string($p) || $p === '' || $this->isExternal($p)) {
                        continue;
                    }
                    if (str_starts_with($p, 'images/')) {
                        $legacy++;
                        if (count($legacySamples) < $sampleLimit) {
                            $legacySamples[] = ['club_id' => $club->id, 'path' => $p];
                        }
                        if (!$this->fileExistsForPublicPath($p)) {
                            $missing++;
                            if (count($missingSamples) < $sampleLimit) {
                                $missingSamples[] = ['club_id' => $club->id, 'expected' => $p];
                            }
                        }
                    } elseif (str_starts_with($p, 'storage/')) {
                        $storage++;
                        if (!$this->fileExistsForPublicPath($p)) {
                            $missing++;
                            if (count($missingSamples) < $sampleLimit) {
                                $missingSamples[] = ['club_id' => $club->id, 'expected' => $p];
                            }
                        }
                    }
                }
            });

            $this->line(" - {$field}: legacy={$legacy}, storage={$storage}, missing={$missing}");

            if (!empty($legacySamples)) {
                $this->line('   Príklady legacy:');
                foreach ($legacySamples as $s) {
                    $this->line('    - Club #' . $s['club_id'] . ' -> ' . $s['path']);
                }
            }
            if (!empty($missingSamples)) {
                $this->line('   Príklady chýbajúcich:');
                foreach ($missingSamples as $s) {
                    $this->line('    - Club #' . $s['club_id'] . ' -> ' . $s['expected']);
                }
            }
        }
    }
}


