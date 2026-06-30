<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Exception;

class TestSmtpConnection extends Command
{
    protected $signature = 'smtp:test 
                            {email? : Email address to test} 
                            {--host= : SMTP Host}
                            {--port= : SMTP Port}
                            {--username= : SMTP Username}
                            {--password= : SMTP Password}
                            {--encryption= : SMTP Encryption (tls/ssl)}
                            {--from-address= : From email address}
                            {--from-name= : From name}
                            {--debug : Show debug information}
                            {--diagnose : Run full diagnostics}
                            {--external : Test with external email provider}';
    
    protected $description = 'Test SMTP connection and email delivery with dynamic configuration';

    public function handle()
    {
        $email = $this->argument('email') ?: 'info@erotikon.sk';
        $debug = $this->option('debug');
        $diagnose = $this->option('diagnose');
        $external = $this->option('external');
        
        $this->info('📧 Testing SMTP connection...');
        $this->line('==========================================');
        
        // Nastavenie dynamickej konfigurácie
        $this->configureSMTPFromOptions();
        
        // Zobrazenie aktuálnej konfigurácie
        $this->displayConfiguration();
        
        if ($diagnose) {
            $this->runDiagnostics();
        }
        
        if ($external) {
            $this->testExternalDelivery($email);
        }
        
        // Test pripojenia
        $this->testConnection($debug);
        
        // Test odoslania emailu
        $this->testEmailSending($email, $debug);
        
        return 0;
    }
    
    private function configureSMTPFromOptions()
    {
        // Ak sú poskytnuté options, používame ich
        if ($this->option('host')) {
            $smtpConfig = [
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $this->option('host'),
                'mail.mailers.smtp.port' => (int) $this->option('port', 587),
                'mail.mailers.smtp.username' => $this->option('username'),
                'mail.mailers.smtp.password' => $this->option('password'),
                'mail.mailers.smtp.encryption' => $this->option('encryption', 'tls'),
                'mail.from.address' => $this->option('from-address', $this->option('username')),
                'mail.from.name' => $this->option('from-name', 'Erotikon Test'),
                'queue.default' => 'sync',
            ];
            
            config($smtpConfig);
            
            // Vyčistíme cache
            app('mail.manager')->purge();
            
            $this->warn('🔧 Použité dynamické nastavenia z options');
        }
    }
    
    private function displayConfiguration()
    {
        $this->info('📋 Current SMTP Configuration:');
        $this->line('Host: ' . config('mail.mailers.smtp.host'));
        $this->line('Port: ' . config('mail.mailers.smtp.port'));
        $this->line('Encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->line('Username: ' . config('mail.mailers.smtp.username'));
        $this->line('Password: ' . (config('mail.mailers.smtp.password') ? '***set***' : 'NOT SET'));
        $this->line('From Address: ' . config('mail.from.address'));
        $this->line('From Name: ' . config('mail.from.name'));
        $this->line('Timeout: ' . config('mail.mailers.smtp.timeout', 'default'));
        $this->line('');
    }
    
    private function runDiagnostics()
    {
        $this->info('🔍 Running SMTP Diagnostics...');
        $this->line('=====================================');
        
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');
        $encryption = config('mail.mailers.smtp.encryption');
        
        // 1. Test DNS rozlíšenia
        $this->comment('1️⃣ Testing DNS resolution...');
        $ip = gethostbyname($host);
        if ($ip !== $host) {
            $this->info("   ✅ {$host} → {$ip}");
        } else {
            $this->error("   ❌ Cannot resolve {$host}");
        }
        
        // 2. Test portov
        $this->comment('2️⃣ Testing port availability...');
        $commonPorts = [25, 465, 587, 2525];
        $availablePorts = [];
        
        foreach ($commonPorts as $testPort) {
            $socket = @fsockopen($host, $testPort, $errno, $errstr, 5);
            if ($socket) {
                $availablePorts[] = $testPort;
                $status = ($testPort == $port) ? "✅ CURRENT" : "✅ available";
                $this->info("   Port {$testPort}: {$status}");
                fclose($socket);
            } else {
                $status = ($testPort == $port) ? "❌ UNAVAILABLE" : "❌ unavailable";
                $this->line("   Port {$testPort}: {$status}");
            }
        }
        
        // 3. Kontrola konfigurácii
        $this->comment('3️⃣ Validating configuration...');
        if (empty($username)) {
            $this->error('   ❌ Username not set');
        } else {
            $this->info("   ✅ Username: {$username}");
        }
        
        if (empty($password)) {
            $this->error('   ❌ Password not set');
        } else {
            $this->info('   ✅ Password: ***set*** (' . strlen($password) . ' chars)');
        }
        
        // 4. Odporúčania
        $this->comment('4️⃣ Recommendations:');
        if (count($availablePorts) > 0) {
            $this->info('   📍 Available ports: ' . implode(', ', $availablePorts));
            if (!in_array($port, $availablePorts)) {
                $recommendedPort = in_array(587, $availablePorts) ? 587 : $availablePorts[0];
                $this->warn("   💡 Recommended port: {$recommendedPort}");
            }
        }
        
        $this->line('');
    }
    
    private function testConnection($debug)
    {
        $this->info('🔌 Testing SMTP connection...');
        
        try {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            $encryption = config('mail.mailers.smtp.encryption');
            $username = config('mail.mailers.smtp.username');
            $password = config('mail.mailers.smtp.password');
            
            if (!$host || !$port || !$username || !$password) {
                $this->error('❌ Missing required SMTP configuration!');
                $this->line('Please set: MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD');
                return false;
            }
            
            if ($debug) {
                $this->line('Debug: Connecting to ' . $host . ':' . $port . ' with ' . $encryption);
            }
            
            // Test socket connection
            $socket = @fsockopen($host, $port, $errno, $errstr, 10);
            if ($socket) {
                $this->info('✅ Socket connection successful!');
                fclose($socket);
            } else {
                $this->error("❌ Socket connection failed: {$errstr} ({$errno})");
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->error('❌ SMTP connection failed!');
            $this->error('Error: ' . $e->getMessage());
            
            // Helpful suggestions
            $this->line('');
            $this->warn('💡 Common solutions:');
            $this->line('1. Check if the host and port are correct');
            $this->line('2. Verify encryption method (ssl/tls)');
            $this->line('3. Check username and password');
            $this->line('4. Ensure firewall allows the port');
            $this->line('5. Contact your hosting provider');
            
            return false;
        }
    }
    
    private function testEmailSending($email, $debug)
    {
        $this->info('📧 Testing email sending...');
        
        try {
            if ($debug) {
                $this->line('Debug: Sending test email to ' . $email);
            }
            
            $testTime = now()->format('d.m.Y H:i:s');
            
            Mail::raw("🔧 SMTP Test z Erotikon.sk\n\n✅ Ak čítate túto správu, SMTP konfigurácia funguje správne!\n\nTestované: {$testTime}\nOdosielateľ: " . config('mail.from.address') . "\nSMTP: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'), function ($message) use ($email, $testTime) {
                $message->to($email)
                        ->subject('🔧 SMTP Test z Erotikon.sk - ' . $testTime);
            });
            
            $this->info('✅ Email sent successfully!');
            $this->line('📧 Sent to: ' . $email);
            $this->line('📤 Time: ' . $testTime);
            
            $this->line('');
            $this->comment('💡 If you don\'t receive the email, check:');
            $this->line('1. SPAM/Junk folder');
            $this->line('2. Correct password (might need App Password)');
            $this->line('3. Hosting allows SMTP on this port');
            $this->line('4. Firewall settings');
            $this->line('5. Sender email address exists on server');
            
            return true;
            
        } catch (Exception $e) {
            $this->error('❌ Email sending failed!');
            $this->error('Error: ' . $e->getMessage());
            
            // Analyze error type
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'Connection refused') !== false) {
                $this->warn('🔧 Firewall or hosting blocks SMTP port');
                $this->warn('🔧 Try different port (25, 465, 587)');
            } elseif (strpos($errorMsg, 'Authentication failed') !== false) {
                $this->warn('🔧 Wrong username or password');
                $this->warn('🔧 For Gmail use App Password instead of regular password');
            } elseif (strpos($errorMsg, 'Could not authenticate') !== false) {
                $this->warn('🔧 SMTP authentication failed');
                $this->warn('🔧 Check username and password');
            } elseif (strpos($errorMsg, 'timed out') !== false) {
                $this->warn('🔧 Timeout - hosting might block SMTP');
                $this->warn('🔧 Try different port or contact hosting');
            }
            
            return false;
        }
    }
    
    private function testExternalDelivery($email)
    {
        $this->info('🌐 Testing external email delivery...');
        
        // Detect email provider
        $provider = 'unknown';
        if (strpos($email, '@gmail.com') !== false) {
            $provider = 'Gmail';
        } elseif (strpos($email, '@outlook.com') !== false || strpos($email, '@hotmail.com') !== false) {
            $provider = 'Outlook/Hotmail';
        } elseif (strpos($email, '@yahoo.com') !== false) {
            $provider = 'Yahoo';
        }
        
        $this->line("📧 Testing delivery to: {$email} ({$provider})");
        
        try {
            // Try to send enhanced test email
            $testTime = now()->format('d.m.Y H:i:s');
            
            Mail::raw("🔧 External SMTP Test z Erotikon.sk\n\n✅ Ak čítate túto správu, SMTP konfigurácia funguje správne pre external doručenie!\n\nTestované: {$testTime}\nOdosielateľ: " . config('mail.from.address') . "\nSMTP: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port') . "\nProvider: {$provider}", function ($message) use ($email, $testTime) {
                $message->to($email)
                        ->subject('🔧 External SMTP Test z Erotikon.sk - ' . $testTime)
                        ->priority(1);
            });
            
            $this->info('✅ External email sent successfully!');
            $this->line('');
            $this->comment('🔍 Next steps:');
            $this->line('1. Check your email in 1-2 minutes');
            $this->line('2. Look in SPAM/Junk folder');
            $this->line('3. If not received, issue is with hosting configuration');
            $this->line('4. Contact your hosting provider');
            
        } catch (Exception $e) {
            $this->error('❌ External email delivery failed!');
            $this->error('Error: ' . $e->getMessage());
        }
    }
} 