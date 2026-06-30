<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class CleanTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:test-data {--force : Vymaže testovacie dáta bez potvrdenia}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vymaže testovacie dáta vytvorené seedermi (testovacích používateľov, inzeráty, platby)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Naozaj chcete vymazať všetky testovacie dáta? Táto akcia je nezvratná!')) {
                $this->info('Operácia zrušená.');
                return 0;
            }
        }

        $this->info('Začínam vymazávanie testovacích dát...');

        DB::beginTransaction();

        try {
            // Najprv nájdeme testovacích používateľov
            $testUsers = User::whereIn('email', [
                'test@example.com',
                'user1@example.com',
                'user2@example.com',
                'user3@example.com',
                'user4@example.com',
                'user5@example.com'
            ])->orWhere('name', 'like', '%Test%')->get();

            $testUserIds = $testUsers->pluck('id')->toArray();

            if (count($testUsers) > 0) {
                $this->info('Našiel som ' . count($testUsers) . ' testovacích používateľov.');
                
                // Nájdeme inzeráty testovacích používateľov
                $testAds = Ad::whereIn('user_id', $testUserIds)->get();
                $testAdIds = $testAds->pluck('id')->toArray();

                $this->info('Našiel som ' . count($testAds) . ' inzerátov testovacích používateľov.');

                // Vymažeme faktúry pre testovacie platby
                if (!empty($testAdIds)) {
                    $testPayments = AdPayment::whereIn('ad_id', $testAdIds)->get();
                    $testPaymentIds = $testPayments->pluck('id')->toArray();

                    if (!empty($testPaymentIds)) {
                        $deletedInvoices = Invoice::whereIn('ad_payment_id', $testPaymentIds)->delete();
                        $this->info("Vymazané faktúry: {$deletedInvoices}");

                        // Vymažeme platby
                        $deletedPayments = AdPayment::whereIn('id', $testPaymentIds)->delete();
                        $this->info("Vymazané platby: {$deletedPayments}");
                    }

                    // Vymažeme inzeráty
                    $deletedAds = Ad::whereIn('id', $testAdIds)->delete();
                    $this->info("Vymazané inzeráty testovacích používateľov: {$deletedAds}");
                }

                // Vymažeme testovacích používateľov
                $deletedUsers = User::whereIn('id', $testUserIds)->delete();
                $this->info("Vymazaní testovacie používatelia: {$deletedUsers}");
            } else {
                $this->info('Nenašli sa žiadni testovacie používatelia.');
            }

            // Vyčistíme aj inzeráty s testovacími názvami (ak nie sú už vymazané)
            $testNicknameAds = Ad::where('nickname', 'LIKE', '%Test%')
                ->orWhere('nickname', 'LIKE', '%Testovací%')
                ->orWhere('nickname', 'LIKE', '%Inzerát%od%')
                ->orWhere('nickname', 'LIKE', '%SMS Test%')
                ->get();

            if ($testNicknameAds->count() > 0) {
                $testNicknameAdIds = $testNicknameAds->pluck('id')->toArray();
                
                // Vymažeme platby pre tieto inzeráty
                $testNicknamePayments = AdPayment::whereIn('ad_id', $testNicknameAdIds)->get();
                if ($testNicknamePayments->count() > 0) {
                    $testNicknamePaymentIds = $testNicknamePayments->pluck('id')->toArray();
                    Invoice::whereIn('ad_payment_id', $testNicknamePaymentIds)->delete();
                    AdPayment::whereIn('id', $testNicknamePaymentIds)->delete();
                }
                
                // Vymažeme inzeráty
                $deletedNicknameAds = Ad::whereIn('id', $testNicknameAdIds)->delete();
                $this->info("Vymazané inzeráty s testovacími názvami: {$deletedNicknameAds}");
            }

            DB::commit();

            $this->info('✅ Testovacie dáta boli úspešne vymazané!');
            $this->info('Odporúčam spustiť: php artisan optimize:clear');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Chyba pri vymazávaní dát: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
