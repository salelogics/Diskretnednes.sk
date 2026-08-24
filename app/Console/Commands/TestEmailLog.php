<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;

class TestEmailLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-log {email? : Email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email logging system by sending test emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'info@diskretnednes.sk';
        
        $this->info("🧪 Testovanie email log systému...");
        $this->info("📧 Testovací email sa pošle na: {$email}");
        
        // Počet emailov pred testom
        $beforeCount = EmailLog::count();
        $this->line("📊 Počet emailov v logu pred testom: {$beforeCount}");
        
        try {
            // Test 1: Jednoduchý textový email
            $this->info("\n🔸 Test 1: Jednoduchý textový email");
            Mail::raw('Toto je testovací email pre overenie email log systému.', function ($message) use ($email) {
                $message->to($email)
                       ->subject('[TEST] Email Log Test - Simple Text');
            });
            $this->info("✅ Test email odoslaný");
            
            // Čakanie na spracovanie
            sleep(1);
            
            // Test 2: HTML email
            $this->info("\n🔸 Test 2: HTML email");
            $htmlContent = '
                <h2>Test HTML Email</h2>
                <p>Toto je <strong>HTML testovací email</strong> pre overenie email log systému.</p>
                <ul>
                    <li>Test logging funkcionality</li>
                    <li>HTML obsah</li>
                    <li>Automatická kategorizácia</li>
                </ul>
            ';
            
            Mail::html($htmlContent, function ($message) use ($email) {
                $message->to($email)
                       ->subject('[TEST] Email Log Test - HTML Content');
            });
            $this->info("✅ HTML email odoslaný");
            
            sleep(1);
            
            // Test 3: Support ticket email
            $this->info("\n🔸 Test 3: Support ticket email");
            Mail::raw('Toto je test odpovede na support ticket.', function ($message) use ($email) {
                $message->to($email)
                       ->subject('Odpoveď na váš support ticket #123 - Test');
            });
            $this->info("✅ Support ticket email odoslaný");
            
            sleep(1);
            
            // Test 4: Uvítací email
            $this->info("\n🔸 Test 4: Uvítací email");
            Mail::raw('Vitajte v aplikácii Diskrétne Dnes!', function ($message) use ($email) {
                $message->to($email)
                       ->subject('Vitajte na DiskretneDnes.sk - Váš účet bol vytvorený');
            });
            $this->info("✅ Uvítací email odoslaný");
            
            sleep(2); // Viac času na spracovanie všetkých emailov
            
            // Overenie výsledkov
            $afterCount = EmailLog::count();
            $newEmails = $afterCount - $beforeCount;
            
            $this->line("\n📊 Výsledky testu:");
            $this->line("📧 Počet emailov v logu po teste: {$afterCount}");
            $this->line("➕ Nové emaily v logu: {$newEmails}");
            
            if ($newEmails >= 4) {
                $this->info("✅ Test úspešný! Všetky emaily boli zalogované.");
            } else {
                $this->warn("⚠️  Možný problém: Očakávané 4 nové emaily, ale zalogované len {$newEmails}");
            }
            
            // Zobrazenie posledných emailov
            $this->line("\n📋 Posledné emaily v logu:");
            $recentEmails = EmailLog::latest()->limit(5)->get();
            
            $this->table(
                ['ID', 'Príjemca', 'Predmet', 'Typ', 'Status', 'Dátum'],
                $recentEmails->map(function ($email) {
                    return [
                        $email->id,
                        $email->to_email,
                        substr($email->subject, 0, 40) . (strlen($email->subject) > 40 ? '...' : ''),
                        $email->type_name,
                        $email->status_name,
                        $email->sent_at ? $email->sent_at->format('d.m.Y H:i') : 'N/A'
                    ];
                })->toArray()
            );
            
            $this->line("\n🔗 Môžete si pozrieť email log na: " . url('/admin/email-log'));
            
        } catch (\Exception $e) {
            $this->error("❌ Chyba pri testovaní email logu:");
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
