<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use App\Models\AdReport;
use App\Services\NotificationService;

class TestAdReportEmail extends Command
{
    protected $signature = 'test:ad-report-email {ad_id=2}';
    protected $description = 'Test skutočného odoslania emailu pre nahlásenie inzerátu';

    public function handle()
    {
        $this->info('📧 Test skutočného odoslania emailu pre nahlásenie inzerátu');
        $this->info('====================================================');
        $this->newLine();

        $adId = $this->argument('ad_id');

        try {
            // 1. Nájdeme inzerát
            $this->info("1. Hľadám inzerát ID {$adId}...");
            $ad = Ad::find($adId);
            
            // Ak zadané ID neexistuje, nájdeme prvý dostupný inzerát
            if (!$ad) {
                $this->warn("❌ Inzerát s ID {$adId} sa nenašiel!");
                $this->info("🔍 Hľadám prvý dostupný inzerát...");
                $ad = Ad::first();
                
                if (!$ad) {
                    $this->error("❌ V databáze nie sú žiadne inzeráty!");
                    return 1;
                }
                
                $this->info("✅ Použijem inzerát ID {$ad->id}: " . ($ad->nickname ?? 'Bez mena'));
            } else {
                $this->info("✅ Inzerát nájdený: " . ($ad->nickname ?? 'Bez mena'));
            }

            // 2. Vytvoríme skutočný test report
            $this->info("2. Vytváram test AdReport...");
            $report = AdReport::create([
                'ad_id' => $ad->id,
                'reason' => 'TEST: Nahlásenie z test command',
                'details' => 'Toto je test email z artisan command aby sme overili že nahlásenie inzerátu skutočne posiela emaily na admin adresu.',
                'reporter_email' => 'test@example.com',  // OPRAVENÉ: reporter_email namiesto email
                'reporter_ip' => '127.0.0.1',           // OPRAVENÉ: reporter_ip namiesto ip_address
                'status' => 'pending'
            ]);

            $this->info("✅ Test AdReport vytvorený s ID: {$report->id}");

            // 3. Odošleme skutočný email cez NotificationService
            $this->info("3. Odosielam skutočný email cez NotificationService...");
            
            $notificationService = app(NotificationService::class);
            $result = $notificationService->newAdReport($report);
            
            if ($result) {
                $this->info("✅ Email úspešne ODOSLANÝ na admin adresu!");
                $this->info("📧 Skontroluj email: info@diskretnednes.sk");
            } else {
                $this->error("❌ Chyba pri odosielaní emailu!");
            }

            // 4. Necháme report v databáze pre kontrolu
            $this->info("4. Test report ponechávaný v databáze (ID: {$report->id})");

            $this->newLine();
            $this->info("🎯 Test dokončený!");
            $this->info("   → Skontroluj email na: info@diskretnednes.sk");
            $this->info("   → Skontroluj admin panel: /admin/nahlasenia-inzeratov");

        } catch (\Exception $e) {
            $this->error("❌ Chyba pri teste: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}