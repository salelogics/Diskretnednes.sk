<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\BlogPost;
use App\Models\EroticClub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MediaRollbackPaths extends Command
{
    protected $signature = 'media:rollback-paths {--dry-run : Only report changes, don\'t write to DB}';
    protected $description = 'Revert DB paths from storage/images/... back to legacy images/... if storage file missing but legacy exists';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info('Rolling back media paths where storage files are missing and legacy exists...');

        $adsFixed = $this->fixAds($dry);
        $postsFixed = $this->fixBlogPosts($dry);
        $clubsFixed = $this->fixClubs($dry);

        $this->line('');
        $this->info("Ads fixed: {$adsFixed}");
        $this->info("BlogPosts fixed: {$postsFixed}");
        $this->info("Clubs fixed: {$clubsFixed}");

        return self::SUCCESS;
    }

    private function toLegacy(string $storagePath): string
    {
        return str_replace('storage/images/', 'images/', $storagePath);
    }

    private function fixAds(bool $dry): int
    {
        $count = 0;
        Ad::orderBy('id')->chunkById(200, function ($ads) use (&$count, $dry) {
            foreach ($ads as $ad) {
                $changed = false;

                // verification_photo
                $p = $ad->verification_photo;
                if (is_string($p) && str_starts_with($p, 'storage/images/')) {
                    $storageAbs = public_path($p);
                    if (!File::exists($storageAbs)) {
                        $legacy = $this->toLegacy($p);
                        if (File::exists(public_path($legacy))) {
                            $ad->verification_photo = $legacy;
                            $changed = true;
                            $this->line("  AD #{$ad->id} verification -> {$legacy}");
                        }
                    }
                }

                // gallery_photos
                $gallery = $ad->gallery_photos;
                if (is_array($gallery) && count($gallery) > 0) {
                    $new = [];
                    $galleryChanged = false;
                    foreach ($gallery as $g) {
                        if (is_string($g) && str_starts_with($g, 'storage/images/')) {
                            $abs = public_path($g);
                            if (!File::exists($abs)) {
                                $legacy = $this->toLegacy($g);
                                if (File::exists(public_path($legacy))) {
                                    $new[] = $legacy;
                                    $galleryChanged = true;
                                    continue;
                                }
                            }
                        }
                        $new[] = $g;
                    }
                    if ($galleryChanged) {
                        $ad->gallery_photos = $new;
                        $changed = true;
                        $this->line("  AD #{$ad->id} gallery fixed");
                    }
                }

                if ($changed) {
                    $count++;
                    if (!$dry) {
                        $ad->save();
                    }
                }
            }
        });
        return $count;
    }

    private function fixBlogPosts(bool $dry): int
    {
        $count = 0;
        BlogPost::orderBy('id')->chunkById(200, function ($posts) use (&$count, $dry) {
            foreach ($posts as $post) {
                $p = $post->image_path;
                if (is_string($p) && str_starts_with($p, 'storage/images/')) {
                    if (!File::exists(public_path($p))) {
                        $legacy = $this->toLegacy($p);
                        if (File::exists(public_path($legacy))) {
                            $count++;
                            $this->line("  Post #{$post->id} -> {$legacy}");
                            if (!$dry) {
                                $post->image_path = $legacy;
                                $post->save();
                            }
                        }
                    }
                }
            }
        });
        return $count;
    }

    private function fixClubs(bool $dry): int
    {
        $count = 0;
        EroticClub::orderBy('id')->chunkById(200, function ($clubs) use (&$count, $dry) {
            foreach ($clubs as $club) {
                $changed = false;
                foreach (['logo_path', 'image_path'] as $field) {
                    $p = $club->{$field};
                    if (is_string($p) && str_starts_with($p, 'storage/images/')) {
                        if (!File::exists(public_path($p))) {
                            $legacy = $this->toLegacy($p);
                            if (File::exists(public_path($legacy))) {
                                $club->{$field} = $legacy;
                                $changed = true;
                                $this->line("  Club #{$club->id} {$field} -> {$legacy}");
                            }
                        }
                    }
                }
                if ($changed) {
                    $count++;
                    if (!$dry) {
                        $club->save();
                    }
                }
            }
        });
        return $count;
    }
}


