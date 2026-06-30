<?php
/**
 * QUEUE WORKER PRE HOSTING
 * 
 * Tento súbor spustí Laravel queue worker cez web rozhranie
 * Použite: https://yourdomain.com/queue-worker.php
 */

// Bezpečnostné overenie
$allowedIPs = [
    '127.0.0.1',
    '::1',
    // Pridajte IP vašej administrácie
];

$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$isAllowed = in_array($clientIP, $allowedIPs) || 
             (isset($_GET['token']) && $_GET['token'] === 'erotikon-queue-2025');

if (!$isAllowed) {
    http_response_code(403);
    die('🚫 Prístup odmietnutý. Neautorizovaný prístup.');
}

// Hlavičky
header('Content-Type: text/plain; charset=utf-8');
echo "🔧 EROTIKON QUEUE WORKER STARTER\n";
echo "================================\n\n";

// Bootstrap Laravel
try {
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        throw new Exception("Autoload súbor neexistuje: $autoload");
    }
    
    require_once $autoload;
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    echo "✅ Laravel bootstrapped úspešne\n";
    
    // Informácie o aktuálnom stave
    $queueConnection = config('queue.default');
    echo "📊 Aktuálna queue konfigurácia: $queueConnection\n\n";
    
    if ($queueConnection === 'sync') {
        echo "ℹ️  QUEUE JE NASTAVENÁ NA SYNC\n";
        echo "   Všetky joby sa spracovávajú okamžite\n";
        echo "   Queue worker nie je potrebný\n\n";
        
        // Ukážeme počet pending a failed jobs
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();
        
        echo "📈 Štatistiky:\n";
        echo "   Pending jobs: $pendingJobs\n";
        echo "   Failed jobs: $failedJobs\n\n";
        
        if ($failedJobs > 0) {
            echo "🧹 Vyčistím failed jobs...\n";
            Artisan::call('queue:flush');
            echo "✅ Failed jobs vyčistené\n";
        }
        
        echo "✅ Všetko je v poriadku!\n";
        
    } else {
        echo "🚀 SPÚŠŤAM QUEUE WORKER...\n";
        echo "   Queue connection: $queueConnection\n";
        echo "   Timeout: 60 sekúnd\n\n";
        
        // Informácie pred spustením
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();
        
        echo "📈 Pred spustením:\n";
        echo "   Pending jobs: $pendingJobs\n";
        echo "   Failed jobs: $failedJobs\n\n";
        
        if ($pendingJobs > 0) {
            echo "⏳ Spracovávam $pendingJobs pending jobs...\n";
            
            // Spusť queue worker s timeout
            $exitCode = Artisan::call('queue:work', [
                '--timeout' => 60,
                '--tries' => 3,
                '--max-jobs' => 50,
                '--stop-when-empty' => true
            ]);
            
            if ($exitCode === 0) {
                echo "✅ Queue worker dokončený úspešne\n";
            } else {
                echo "❌ Queue worker skončil s chybou (exit code: $exitCode)\n";
            }
            
            // Stav po spracovaní
            $remainingJobs = DB::table('jobs')->count();
            echo "📊 Zostávajúce jobs: $remainingJobs\n";
            
        } else {
            echo "✅ Žiadne pending jobs na spracovanie\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ CHYBA: " . $e->getMessage() . "\n";
    echo "📁 Súbor: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🏁 Queue worker dokončený\n";
echo "⏰ Čas: " . date('d.m.Y H:i:s') . "\n";
echo "\n💡 Pre opätovné spustenie obnovte túto stránku\n";
echo "💡 Pre bezpečnosť použite: ?token=erotikon-queue-2025\n";
?> 