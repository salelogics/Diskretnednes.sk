<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ad;

$id = isset($argv[1]) ? (int)$argv[1] : null;
if (!$id) {
    fwrite(STDERR, "Usage: php dev/find_ad_media.php <ad_id>\n");
    exit(1);
}

$ad = Ad::find($id);
if (!$ad) {
    echo "null\n";
    exit(0);
}

$out = [
    'id' => $ad->id,
    'verification' => $ad->verification_photo,
    'gallery' => $ad->gallery_photos,
    'video' => $ad->video,
];

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";


