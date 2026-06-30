<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionExpiredMail;
use App\Models\Ad;
use App\Models\User;

class TestExpiredSubscriptionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-expired-subscription {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email pre vypršané predplatné s bankovými údajmi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        
        $this->info('🧪 Testovanie emailu pre vypršané predplatné');
        $this->line('=' . str_repeat('=', 50));
        
        try {
            // Vytvoríme mock objekt alebo použijeme existujúci inzerát
            $ad = Ad::with('user')->first();
            
            if (!$ad) {
                // Ak neexistuje žiadny inzerát, vytvoríme mock objekt
                $mockUser = new User([
                    'name' => 'Anna Nováková',
                    'email' => $email,
                    'id' => 123
                ]);
                
                $ad = new Ad([
                    'id' => 16021,
                    'views' => 173856,
                    'nickname' => 'Sexy masérka Anna',
                    'city' => 'bratislava',
                    'subscription_expires_at' => now()->subDays(1),
                    'user_id' => 123
                ]);
                
                $ad->setRelation('user', $mockUser);
            } else {
                // Ak máme reálny inzerát, použijeme ho ale zmeníme email
                $ad->user->email = $email;
                $ad->views = $ad->views ?: 173856;
            }
            
            $this->info("📧 Posielam email na: {$email}");
            $this->info("📋 Inzerát č.: {$ad->id}");
            $this->info("👤 Používateľ: {$ad->user->name}");
            $this->info("📊 Počet zobrazení: " . number_format($ad->views));
            $this->line('');
            
            // Odošleme email
            Mail::to($email)->send(new SubscriptionExpiredMail($ad));
            
            $this->info('✅ Email úspešne odoslaný!');
            $this->line('');
            $this->info('📝 Obsah emailu obsahuje:');
            $this->line('   ⛔ Výrazné upozornenie o vypršaní predplatného');
            $this->line('   📊 Štatistiky návštevnosti inzerátu');
            $this->line('   🏦 Bankové údaje pre VÚB a Slovenskú sporiteľňu');
            $this->line("   💳 Variabilný symbol: 2022{$ad->id}");
            $this->line('   💰 Suma: 40 EUR za 25 dní CLASSIC');
            $this->line('   📱 Možnosti platby (SMS, bankový prevod, online)');
            $this->line('   🌟 Výhody inzercie na Erotikon.sk');
            $this->line('   📞 Kontaktné údaje: admin@erotikon.sk');
            $this->line('');
            $this->info('🎨 Dizajn emailu:');
            $this->line('   🔴 Červená téma pre urgentnosť');
            $this->line('   📱 Responzívny dizajn');
            $this->line('   🎯 Gradient tlačidlá s hover efektmi');
            $this->line('   📋 Prehľadné bankové údaje v mriežke');
            $this->line('   ⚠️ Výrazné upozornenia a tipy');
            $this->line('');
            $this->warn('🌐 Ak používate Mailpit, otvorte: http://localhost:8025/');
            
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri posielaní emailu: ' . $e->getMessage());
            $this->error('📍 Súbor: ' . $e->getFile());
            $this->error('📍 Riadok: ' . $e->getLine());
            return 1;
        }
        
        return 0;
    }
} 