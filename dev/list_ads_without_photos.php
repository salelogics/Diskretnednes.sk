<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ad;

$mode = 'db';
foreach ($argv as $arg) {
    if ($arg === '--mode=effective') {
        $mode = 'effective';
    }
}

$results = [];
$total = 0;

Ad::query()->orderBy('id')->chunk(500, function ($ads) use (&$results, &$total, $mode) {
    foreach ($ads as $ad) {
        $total++;
        $hasVerification = is_string($ad->verification_photo) && trim((string) $ad->verification_photo) !== '';
        $hasGallery = is_array($ad->gallery_photos) && count(array_filter($ad->gallery_photos ?? [], function ($p) {
            return is_string($p) && trim((string) $p) !== '';
        })) > 0;

        $missingByDb = (!$hasVerification && !$hasGallery);

        if ($mode === 'db') {
            if ($missingByDb) {
                $results[] = [
                    'id' => $ad->id,
                    'nickname' => $ad->nickname,
                    'verification' => $ad->verification_photo,
                    'gallery_count' => is_array($ad->gallery_photos) ? count($ad->gallery_photos) : 0,
                ];
            }
        } else {
            // effective mode: využij accessors (kontrolujú existenciu súborov)
            $verificationUrl = $ad->verification_image_url; // accessor
            $galleryUrls = $ad->gallery_image_urls; // accessor
            $hasAnyEffective = (is_string($verificationUrl) && $verificationUrl !== '') || (is_array($galleryUrls) && count($galleryUrls) > 0);
            if (!$hasAnyEffective) {
                $results[] = [
                    'id' => $ad->id,
                    'nickname' => $ad->nickname,
                    'verification' => $ad->verification_photo,
                    'gallery_raw' => $ad->gallery_photos,
                ];
            }
        }
    }
});

echo json_encode([
    'mode' => $mode,
    'total_ads' => $total,
    'missing_count' => count($results),
    'ads' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";


