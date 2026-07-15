<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\PublicAdsController;

class TestAdReportPopup extends Command
{
    protected $signature = 'test:ad-report-popup {ad_id=2}';
    protected $description = 'Test nahlásenie inzerátu ako popup - simuluje HTTP request';

    public function handle()
    {
        $this->info('🌐 Test popup nahlásenia inzerátu (simulácia HTTP request)');
        $this->info('===========================================================');
        $this->newLine();

        $adId = $this->argument('ad_id');

        try {
            // 1. Nájdeme inzerát alebo použijeme prvý dostupný
            $this->info("1. Hľadám inzerát ID {$adId}...");
            $ad = Ad::find($adId);
            
            if (!$ad) {
                $this->warn("❌ Inzerát s ID {$adId} sa nenašiel!");
                $this->info("🔍 Hľadám prvý dostupný inzerát...");
                $ad = Ad::first();
                
                if (!$ad) {
                    $this->error("❌ V databáze nie sú žiadne inzeráty!");
                    return 1;
                }
                
                $this->info("✅ Použijem inzerát ID {$ad->id}: " . ($ad->nickname ?? 'Bez mena'));
                $adId = $ad->id;
            } else {
                $this->info("✅ Inzerát nájdený: " . ($ad->nickname ?? 'Bez mena'));
            }

            // 2. Vytvoríme mock request ako by ho poslal JavaScript
            $this->info("2. Vytváram mock HTTP request...");
            
            $requestData = [
                'reason' => 'TEST: Nevhodný obsah',
                'details' => 'Toto je test popup nahlásenia z artisan commandu. Simuluje presne to isté čo JavaScript popup.',
                'email' => 'test@example.com'
            ];
            
            $this->info("📡 Request data:");
            $this->line("   - reason: " . $requestData['reason']);
            $this->line("   - details: " . substr($requestData['details'], 0, 50) . '...');
            $this->line("   - email: " . $requestData['email']);

            // 3. Vytvoríme mock request objekt - SIMULUJEME JSON AKO JAVASCRIPT  
            $randomIp = '192.168.1.' . rand(1, 254); // Random IP pre obídenie duplicity check
            $jsonData = json_encode($requestData);
            $request = Request::create("/inzerat/{$adId}/nahlas", 'POST', [], [], [], 
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_CONTENT_TYPE' => 'application/json',
                    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
                    'REMOTE_ADDR' => $randomIp,
                    'HTTP_USER_AGENT' => 'Test Command Browser'
                ], 
                $jsonData
            );
            
            // Nastavíme správne headers ako JavaScript
            $request->headers->set('Content-Type', 'application/json');
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            
            // KRITICKÉ: Parsujeme JSON data do request parametrov (ako Laravel middleware)
            $request->merge(json_decode($jsonData, true));

            // 4. Zavoláme kontrolér
            $this->info("3. Volám PublicAdsController::report()...");
            
            $controller = app(PublicAdsController::class);
            $response = $controller->report($request, $adId);
            
            // 5. Analyzujeme odpoveď
            $responseData = $response->getData(true);
            $statusCode = $response->getStatusCode();
            
            $this->info("4. Výsledok:");
            $this->line("   📊 HTTP Status: {$statusCode}");
            
            if ($statusCode === 200 && isset($responseData['success']) && $responseData['success']) {
                $this->info("   ✅ SUCCESS: " . ($responseData['message'] ?? 'OK'));
                $this->newLine();
                $this->info("🎯 Test úspešný! Popup simulácia funguje správne.");
                $this->info("📧 Skontroluj email na: info@diskretnednes.sk");
                return 0;
            } else {
                $this->error("   ❌ FAILED: " . ($responseData['message'] ?? 'Unknown error'));
                $this->newLine();
                $this->error("❌ Test neúspešný! Problém v kontroléri.");
                $this->line("Response data: " . json_encode($responseData, JSON_PRETTY_PRINT));
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("💥 EXCEPTION: " . $e->getMessage());
            $this->newLine();
            $this->line("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}