<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use App\Models\AdReport;
use App\Services\NotificationService;
use App\Helpers\AdminNotificationHelper;

class TestAdReport extends Command
{
    protected $signature = 'test:ad-report {ad_id=1219}';
    protected $description = 'Test nahlásenie inzerátu a odosielanie emailov';

    public function handle()
    {
        $this->info('🔧 Test nahlásenia inzerátu');
        $this->info('==========================');
        $this->newLine();

        $adId = $this->argument('ad_id');

        try {
            // 1. Test - nájdeme inzerát
            $this->info("1. Hľadám inzerát ID {$adId}...");
            $ad = Ad::find($adId);
            if (!$ad) {
                $this->error("❌ Inzerát s ID {$adId} sa nenašiel!");
                return 1;
            }
            $this->info("✅ Inzerát nájdený: " . ($ad->nickname ?? 'Bez mena'));
            $this->newLine();

            // 2. Test - admin emaily
            $this->info("2. Testujem AdminNotificationHelper...");
            $adminEmails = AdminNotificationHelper::getAdminEmails();
            $this->info("✅ Admin emaily: " . implode(', ', $adminEmails));
            $this->newLine();

            // 3. Test - SMTP konfigurácia
            $this->info("3. Kontrolujem SMTP konfiguráciu...");
            $this->line("Mail driver: " . config('mail.default'));
            $this->line("SMTP host: " . config('mail.mailers.smtp.host'));
            $this->line("SMTP port: " . config('mail.mailers.smtp.port'));
            $this->line("SMTP username: " . config('mail.mailers.smtp.username'));
            $this->line("From address: " . config('mail.from.address'));
            $this->newLine();

            // 4. Test - vytvoríme AdReport
            $this->info("4. Vytváram test AdReport...");
            $report = AdReport::create([
                'ad_id' => $adId,
                'reason' => 'Test nahlásenie z Artisan command',
                'details' => 'Toto je test nahlásenie pre diagnostiku emailov',
                'reporter_ip' => '127.0.0.1',
                'reporter_email' => 'test@example.com',
                'status' => 'pending'
            ]);
            $this->info("✅ AdReport vytvorený s ID: " . $report->id);
            $this->newLine();

            // 5. Test - NotificationService
            $this->info("5. Testujem NotificationService...");
            $notificationService = app(NotificationService::class);
            $this->info("✅ NotificationService instance vytvorená");

            // 6. Test - newAdReport metóda
            $this->info("6. Volám NotificationService::newAdReport()...");
            $result = $notificationService->newAdReport($report);
            $this->info("✅ NotificationService::newAdReport() úspešne dokončené");
            $this->line("Výsledok: " . ($result ? 'Success' : 'Failed'));
            $this->newLine();

            // 7. Test - vymazanie test reportu  
            $this->info("7. Mažem test report...");
            $report->delete();
            $this->info("✅ Test report vymazaný");
            $this->newLine();

            $this->info("🎉 Všetky testy úspešne dokončené!");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ CHYBA: " . $e->getMessage());
            $this->line("File: " . $e->getFile() . ":" . $e->getLine());
            $this->newLine();
            $this->line("Stack trace:");
            $this->line($e->getTraceAsString());
            
            // Pokus o vymazanie reportu ak existuje
            if (isset($report) && $report->id) {
                try {
                    $report->delete();
                    $this->info("✅ Test report vymazaný po chybe");
                } catch (\Exception $cleanupError) {
                    $this->error("❌ Nepodarilo sa vymazať test report: " . $cleanupError->getMessage());
                }
            }
            
            return 1;
        }
    }
}