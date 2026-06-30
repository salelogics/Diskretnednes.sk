<?php
declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\EroticClub;
use App\Models\Ad;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

ini_set('memory_limit', '1024M');
set_time_limit(0);

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

// Bootstrap Laravel (so facades like DB, Storage, Artisan work)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/**
 * Normalize blog image path to new convention: storage/blog/images/{filename}
 */
function normalizeBlogImagePath(?string $path): ?string
{
    if (!$path) {
        return null;
    }

    $trimmed = ltrim($path, '/');

    if (str_starts_with($trimmed, 'storage/blog/images/')) {
        return $trimmed; // already new form
    }

    if (str_starts_with($trimmed, 'storage/images/blog/')) {
        return 'storage/blog/images/' . basename($trimmed);
    }

    if (str_starts_with($trimmed, 'images/blog/images/')) {
        return 'storage/blog/images/' . basename($trimmed);
    }

    if (str_starts_with($trimmed, 'images/blog/')) {
        return 'storage/blog/images/' . basename($trimmed);
    }

    if (strpos($trimmed, '/') === false) {
        // plain filename in DB
        return 'storage/blog/images/' . $trimmed;
    }

    return $trimmed; // leave unknown/custom paths as-is
}

/**
 * Replace old blog image URL patterns in Editor.js HTML/JSON content to new /storage/blog/images/
 */
function rewriteBlogContentImages(?string $content): ?string
{
    if ($content === null || $content === '') {
        return $content;
    }

    $replacements = [
        // Absolute URLs with domain
        '~https?://[^\s"\']+/(storage/images/blog/)~i' => '/storage/blog/images/',
        '~https?://[^\s"\']+/(images/blog/images/)~i' => '/storage/blog/images/',
        '~https?://[^\s"\']+/(images/blog/)~i' => '/storage/blog/images/',
        '~https?://[^\s"\']+/(images/uploads/blog/)~i' => '/storage/blog/images/',

        // Root-relative URLs
        '~/(storage/images/blog/)~i' => '/storage/blog/images/',
        '~/(images/blog/images/)~i' => '/storage/blog/images/',
        '~/(images/blog/)~i' => '/storage/blog/images/',
        '~/(images/uploads/blog/)~i' => '/storage/blog/images/',
    ];

    $updated = $content;
    foreach ($replacements as $pattern => $target) {
        $updated = preg_replace($pattern, $target, $updated);
    }

    return $updated;
}

/**
 * Normalize club paths to new convention:
 *  - images -> storage/clubs/{filename}
 *  - logos  -> storage/clubs/logos/{filename}
 */
function normalizeClubPath(?string $path, bool $isLogo): ?string
{
    if (!$path) {
        return null;
    }

    $trimmed = ltrim($path, '/');
    $logoDir = $isLogo ? 'storage/clubs/logos/' : 'storage/clubs/';

    // Already new
    if (str_starts_with($trimmed, $logoDir)) {
        return $trimmed;
    }

    // Old storage-based paths
    if ($isLogo && str_starts_with($trimmed, 'storage/images/uploads/clubs/logos/')) {
        return 'storage/clubs/logos/' . basename($trimmed);
    }
    if (!$isLogo && str_starts_with($trimmed, 'storage/images/uploads/clubs/')) {
        return 'storage/clubs/' . basename($trimmed);
    }

    // Old public images paths
    if ($isLogo && str_starts_with($trimmed, 'images/uploads/clubs/logos/')) {
        return 'storage/clubs/logos/' . basename($trimmed);
    }
    if (!$isLogo && str_starts_with($trimmed, 'images/uploads/clubs/')) {
        return 'storage/clubs/' . basename($trimmed);
    }

    // Generic fallbacks
    if (!$isLogo && (str_starts_with($trimmed, 'images/clubs/') || str_starts_with($trimmed, 'storage/images/clubs/'))) {
        return 'storage/clubs/' . basename($trimmed);
    }
    if ($isLogo && (str_starts_with($trimmed, 'images/clubs/logos/') || str_starts_with($trimmed, 'storage/images/clubs/logos/'))) {
        return 'storage/clubs/logos/' . basename($trimmed);
    }

    if (strpos($trimmed, '/') === false) {
        return $logoDir . $trimmed; // plain filename
    }

    return $trimmed;
}

// Ensure public/storage symlink exists
$publicStorage = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage';
if (!is_dir($publicStorage) && !is_link($publicStorage)) {
    echo "Creating storage symlink...\n";
    try {
        Artisan::call('storage:link');
        echo Artisan::output();
    } catch (Throwable $e) {
        echo "storage:link failed: " . $e->getMessage() . "\n";
    }
}

// BLOG POSTS
$blogUpdated = 0;
$blogContentUpdated = 0;

BlogPost::query()->orderBy('id')->chunk(200, function ($posts) use (&$blogUpdated, &$blogContentUpdated) {
    foreach ($posts as $post) {
        $changed = false;

        // image_path normalization
        $newImagePath = normalizeBlogImagePath($post->image_path);
        if ($newImagePath && $newImagePath !== $post->image_path) {
            $post->image_path = $newImagePath;
            $changed = true;
        }

        // content rewrite only for local posts (wp_id null)
        if ($post->wp_id === null && is_string($post->content) && $post->content !== '') {
            $newContent = rewriteBlogContentImages($post->content);
            if ($newContent !== $post->content) {
                $post->content = $newContent;
                $changed = true;
                $blogContentUpdated++;
            }
        }

        if ($changed) {
            $post->save();
            $blogUpdated++;
        }
    }
});

// CLUBS
$clubsUpdated = 0;

EroticClub::query()->orderBy('id')->chunk(200, function ($clubs) use (&$clubsUpdated) {
    foreach ($clubs as $club) {
        $changed = false;

        // image_path
        $newImage = normalizeClubPath($club->image_path, false);
        if ($newImage && $newImage !== $club->image_path) {
            // Update only if the target file likely exists in new location
            $candidate = str_starts_with($newImage, 'storage/') ? substr($newImage, strlen('storage/')) : $newImage;
            if (Storage::disk('public')->exists($candidate)) {
                $club->image_path = $newImage;
                $changed = true;
            }
        }

        // logo_path
        $newLogo = normalizeClubPath($club->logo_path, true);
        if ($newLogo && $newLogo !== $club->logo_path) {
            $candidate = str_starts_with($newLogo, 'storage/') ? substr($newLogo, strlen('storage/')) : $newLogo;
            if (Storage::disk('public')->exists($candidate)) {
                $club->logo_path = $newLogo;
                $changed = true;
            }
        }

        if ($changed) {
            $club->save();
            $clubsUpdated++;
        }
    }
});

echo "Done. Updated records:\n";
echo " - Blog posts (image_path/content): {$blogUpdated} / content changed: {$blogContentUpdated}\n";
echo " - Clubs (image/logo): {$clubsUpdated}\n";
// ADS
$adsUpdated = 0;
$adsGalleryUpdated = 0;

Ad::query()->orderBy('id')->chunk(200, function ($ads) use (&$adsUpdated, &$adsGalleryUpdated) {
    foreach ($ads as $ad) {
        $changed = false;

        // verification_photo
        $verification = $ad->verification_photo;
        if ($verification) {
            $trimmed = ltrim($verification, '/');
            $newVerification = $trimmed;
            if (str_starts_with($trimmed, 'storage/ads/verification/')) {
                // ok
            } elseif (str_starts_with($trimmed, 'storage/images/uploads/ads/verification/')) {
                $newVerification = 'storage/ads/verification/' . basename($trimmed);
            } elseif (str_starts_with($trimmed, 'images/uploads/ads/verification/')) {
                $newVerification = 'storage/ads/verification/' . basename($trimmed);
            } elseif (str_starts_with($trimmed, 'ads/verification/')) {
                $newVerification = 'storage/ads/verification/' . basename($trimmed);
            } elseif (strpos($trimmed, '/') === false) {
                $newVerification = 'storage/ads/verification/' . $trimmed;
            }

            if ($newVerification !== $verification) {
                // update len ak fyzicky existuje v public disk
                $candidate = str_starts_with($newVerification, 'storage/') ? substr($newVerification, strlen('storage/')) : $newVerification;
                if (Storage::disk('public')->exists($candidate)) {
                    $ad->verification_photo = $newVerification;
                    $changed = true;
                }
            }
        }

        // gallery_photos
        $gallery = $ad->gallery_photos;
        if (is_array($gallery) && !empty($gallery)) {
            $newGallery = [];
            $galleryChanged = false;
            foreach ($gallery as $g) {
                $trimmed = ltrim((string) $g, '/');
                $newG = $trimmed;
                if (str_starts_with($trimmed, 'storage/ads/gallery/')) {
                    // ok
                } elseif (str_starts_with($trimmed, 'storage/images/uploads/ads/gallery/')) {
                    $newG = 'storage/ads/gallery/' . basename($trimmed);
                } elseif (str_starts_with($trimmed, 'images/uploads/ads/gallery/')) {
                    $newG = 'storage/ads/gallery/' . basename($trimmed);
                } elseif (str_starts_with($trimmed, 'ads/gallery/')) {
                    $newG = 'storage/ads/gallery/' . basename($trimmed);
                } elseif (strpos($trimmed, '/') === false) {
                    $newG = 'storage/ads/gallery/' . $trimmed;
                }

                if ($newG !== $g) {
                    $candidate = str_starts_with($newG, 'storage/') ? substr($newG, strlen('storage/')) : $newG;
                    if (Storage::disk('public')->exists($candidate)) {
                        $newGallery[] = $newG;
                        $galleryChanged = true;
                        continue;
                    }
                }
                $newGallery[] = $g; // no change or not exists
            }

            if ($galleryChanged) {
                $ad->gallery_photos = $newGallery;
                $changed = true;
                $adsGalleryUpdated++;
            }
        }

        // video
        $video = $ad->video;
        if (is_string($video) && $video !== '') {
            $trimmed = ltrim($video, '/');
            $newVideo = $trimmed;
            if (str_starts_with($trimmed, 'storage/ads/videos/')) {
                // ok
            } elseif (str_starts_with($trimmed, 'storage/images/uploads/ads/videos/')) {
                $newVideo = 'storage/ads/videos/' . basename($trimmed);
            } elseif (str_starts_with($trimmed, 'images/uploads/ads/videos/')) {
                $newVideo = 'storage/ads/videos/' . basename($trimmed);
            } elseif (str_starts_with($trimmed, 'ads/videos/')) {
                $newVideo = 'storage/ads/videos/' . basename($trimmed);
            }

            if ($newVideo !== $video) {
                $candidate = str_starts_with($newVideo, 'storage/') ? substr($newVideo, strlen('storage/')) : $newVideo;
                if (Storage::disk('public')->exists($candidate)) {
                    $ad->video = $newVideo;
                    $changed = true;
                }
            }
        }

        if ($changed) {
            $ad->save();
            $adsUpdated++;
        }
    }
});

echo " - Ads (verification/gallery/video): {$adsUpdated} / gallery updated: {$adsGalleryUpdated}\n";