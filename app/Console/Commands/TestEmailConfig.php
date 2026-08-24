<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\User;

class TestEmailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email : Email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('🔧 Testujem emailovú konfiguráciu...');
        $this->info("📧 Posielam testovací email na: {$email}");
        
        try {
            // Vytvoríme mock user pre test
            $testUser = new User([
                'name' => 'Test User',
                'email' => $email,
                'created_at' => now()
            ]);
            
            // Pokus o odoslanie emailu
            Mail::to($email)->send(new WelcomeMail($testUser));
            
            $this->info('✅ Email bol úspešne odoslaný!');
            $this->info('📋 Konfigurácia emailov je správna.');
            
            // Zobrazenie konfigurácie
            $this->line('');
            $this->info('📋 Aktuálna konfigurácia:');
            $this->line('- Mailer: ' . config('mail.default'));
            $this->line('- Host: ' . config('mail.mailers.smtp.host'));
            $this->line('- Port: ' . config('mail.mailers.smtp.port'));
            $this->line('- Encryption: ' . config('mail.mailers.smtp.encryption', 'none'));
            $this->line('- Username: ' . (config('mail.mailers.smtp.username') ? '***nastavené***' : 'nie je nastavené'));
            $this->line('- From Address: ' . config('mail.from.address'));
            $this->line('- From Name: ' . config('mail.from.name'));
            
        } catch (\Exception $e) {
            $this->error('❌ Chyba pri odosielaní emailu!');
            $this->error('💬 Chybová správa: ' . $e->getMessage());
            $this->line('');
            $this->error('🔧 Skontrolujte .env konfiguráciu:');
            $this->line('- MAIL_MAILER=smtp');
            $this->line('- MAIL_HOST=your-smtp-host');
            $this->line('- MAIL_PORT=587');
            $this->line('- MAIL_USERNAME=your-email@domain.com');
            $this->line('- MAIL_PASSWORD=your-password');
            $this->line('- MAIL_ENCRYPTION=tls');
            $this->line('- MAIL_FROM_ADDRESS=no-reply@domain.com');
            $this->line('- MAIL_FROM_NAME="DiskretneDnes.sk"');
            
            return 1;
        }
        
        return 0;
    }
}
