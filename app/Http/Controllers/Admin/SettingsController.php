<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\EmailLog;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = $this->getEnvSettings();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            // Stripe nastavenia
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
            
            // Google OAuth
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
            
            // Facebook OAuth
            'facebook_client_id' => 'nullable|string',
            'facebook_client_secret' => 'nullable|string',
            
            // Google Analytics & Tag Manager
            'google_analytics_id' => 'nullable|string',
            'google_tag_manager_id' => 'nullable|string',
            
            // Platobné nastavenia
            'payment_iban' => 'nullable|string',
            
            // SMS platby nastavenia
            'sms_payment_pid' => 'nullable|string',
            'sms_payment_key' => 'nullable|string',
            'sms_payment_url' => 'nullable|url',
            
            // Nastavenia faktúr
            'invoice_company_name' => 'nullable|string|max:255',
            'invoice_company_address' => 'nullable|string|max:255',
            'invoice_company_city' => 'nullable|string|max:100',
            'invoice_company_postal_code' => 'nullable|string|max:20',
            'invoice_company_country' => 'nullable|string|max:100',
            'invoice_company_ico' => 'nullable|string|max:20',
            'invoice_company_dic' => 'nullable|string|max:20',
            'invoice_company_ic_dph' => 'nullable|string|max:20',
            'invoice_company_phone' => 'nullable|string|max:50',
            'invoice_company_email' => 'nullable|email|max:255',
            'invoice_company_website' => 'nullable|string|max:255',
            'invoice_bank_name' => 'nullable|string|max:255',
            'invoice_bank_account' => 'nullable|string|max:50',
            'invoice_bank_iban' => 'nullable|string|max:50',
            'invoice_bank_swift' => 'nullable|string|max:20',
            'invoice_constant_symbol' => 'nullable|string|max:10',
            
            // Email nastavenia
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            
            // Admin notifikácie
            'admin_notification_emails' => 'nullable|string',
            
            // SMTP nastavenia
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            
            // Aplikačné nastavenia
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'ga_service_account' => 'nullable|file|mimes:json,txt,application/json',
        ]);

        try {
            // Spracovanie uploadu Google Analytics JSON kľúča
            if ($request->hasFile('ga_service_account')) {
                $file = $request->file('ga_service_account');
                $path = storage_path('app/analytics');
                if (!is_dir($path)) {
                    mkdir($path, 0775, true);
                }
                $file->move($path, 'service-account-credentials.json');
            }
            // Uložíme nastavenia do databázy namiesto .env súboru
            $this->updateDatabaseSettings($request->all());
            
            // Vyčistenie cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            \App\Models\Setting::clearCache();
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'Nastavenia boli úspešne aktualizované a uložené do databázy!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Chyba pri aktualizácii nastavení: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $errors = [];
        $details = [];

        try {
            // Dynamicky nastavíme SMTP konfiguráciu z formulára
            $this->configureSMTPFromRequest($request);

            // Detailná diagnostika
            $details[] = "🔧 SMTP Konfigurácia:";
            $details[] = "   Host: " . config('mail.mailers.smtp.host');
            $details[] = "   Port: " . config('mail.mailers.smtp.port');
            $details[] = "   Encryption: " . (config('mail.mailers.smtp.encryption') ?: 'žiadne');
            $details[] = "   Username: " . (config('mail.mailers.smtp.username') ? '***nastavené***' : 'nie je nastavené');
            $details[] = "   Password: " . (config('mail.mailers.smtp.password') ? '***nastavené***' : 'nie je nastavené');
            $details[] = "   From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")";

            // Test SMTP spojenia pomocou raw socket
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            
            $details[] = "\n🌐 Test SMTP spojenia:";
            
            // Socket test
            $socketResult = @fsockopen($host, $port, $errno, $errstr, 10);
            if ($socketResult) {
                $details[] = "   ✅ Socket spojenie na {$host}:{$port} úspešné";
                fclose($socketResult);
            } else {
                $errors[] = "   ❌ Socket spojenie zlyhalo: {$errstr} ({$errno})";
                $details[] = "   ❌ Nemožno sa pripojiť na {$host}:{$port}";
            }

            // Test DNS rozlíšenie
            $ip = gethostbyname($host);
            if ($ip !== $host) {
                $details[] = "   ✅ DNS rozlíšenie: {$host} → {$ip}";
            } else {
                $errors[] = "   ❌ DNS rozlíšenie zlyhalo pre {$host}";
            }

            $details[] = "\n📧 Test odosielania emailu:";

            // Vyčistíme Laravel mail manager cache
            app('mail.manager')->purge();

            // Pošleme testovací email s detailným logovaním
            \Mail::raw('Toto je testovací email z admin nastavení Erotikon aplikácie.\n\nAk ste dostali tento email, SMTP nastavenia fungujú správne.\n\nTestované: ' . now()->format('d.m.Y H:i:s'), function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('🔧 Test SMTP - Erotikon [' . now()->format('d.m.Y H:i:s') . ']');
            });

            $details[] = "   ✅ Email bol úspešne odoslaný cez Laravel Mail";
            $details[] = "   📧 Adresa: " . $request->test_email;
            $details[] = "   📤 Čas odoslania: " . now()->format('d.m.Y H:i:s');

            // Dodatočné rady
            $tips = [
                "\n💡 Ak email nepríde, skontrolujte:",
                "   1. SPAM/Junk priečinok v emailovom klientovi",
                "   2. Či je heslo správne (možno potrebuje App Password)",
                "   3. Či hosting umožňuje SMTP na porte {$port}",
                "   4. Firewall nastavenia",
                "   5. Či email adresa odosielateľa existuje na serveri"
            ];

            return response()->json([
                'success' => true,
                'message' => 'Test bol úspešne dokončený! Email odoslaný na ' . $request->test_email,
                'details' => implode("\n", array_merge($details, $tips)),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            $errorDetails = [
                "❌ Chyba pri odosielaní emailu:",
                "   Správa: " . $e->getMessage(),
                "   Súbor: " . $e->getFile() . ":" . $e->getLine(),
                "   Trace: " . $e->getTraceAsString()
            ];

            // Loguj podrobné informácie pre debug
            \Log::error('SMTP Test Error Details', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'smtp_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'username' => config('mail.mailers.smtp.username'),
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ],
                'email_log_count_before' => EmailLog::count(),
                'queue_driver' => config('queue.default'),
                'mail_driver' => config('mail.default'),
            ]);

            // Analýza typu chyby
            $errorMsg = $e->getMessage();
            $suggestions = [];

            if (strpos($errorMsg, 'Connection refused') !== false) {
                $suggestions[] = "🔧 Firewall alebo hosting blokuje SMTP port";
                $suggestions[] = "🔧 Skúste iný port (25, 465, 587)";
            } elseif (strpos($errorMsg, 'Authentication failed') !== false) {
                $suggestions[] = "🔧 Nesprávne používateľské meno alebo heslo";
                $suggestions[] = "🔧 Pre Gmail použite App Password namiesto bežného hesla";
            } elseif (strpos($errorMsg, 'Could not authenticate') !== false) {
                $suggestions[] = "🔧 SMTP autentifikácia zlyhala";
                $suggestions[] = "🔧 Skontrolujte username a password";
            } elseif (strpos($errorMsg, 'timed out') !== false) {
                $suggestions[] = "🔧 Timeout - hosting možno blokuje SMTP";
                $suggestions[] = "🔧 Skúste iný port alebo kontaktujte hosting";
            } elseif (strpos($errorMsg, 'No supported encryptor found') !== false) {
                $suggestions[] = "🔧 SSL/TLS chyba - skúste iný encryption typ";
                $suggestions[] = "🔧 Skúste port 587 s TLS namiesto 465 s SSL";
            }

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri testovaní SMTP: ' . $e->getMessage(),
                'details' => implode("\n", array_merge($details, $errorDetails, $suggestions)),
                'errors' => array_merge($errors, [$e->getMessage()])
            ], 422);
        }
    }

    public function testAdminNotifications(Request $request)
    {
        try {
            // Dynamicky nastavíme SMTP konfiguráciu z formulára
            $this->configureSMTPFromRequest($request);

            // Vyčistíme Laravel mail manager cache
            app('mail.manager')->purge();

            // Debug: Zobraziť aktuálnu konfiguráciu
            \Log::info('Admin notifications test - current config', [
                'admin_notification_emails_env' => env('ADMIN_NOTIFICATION_EMAILS'),
                'admin_notification_emails_config' => config('app.admin_notification_emails'),
                'admin_notification_emails_request' => $request->input('admin_notification_emails'),
                'mail_driver' => config('mail.default'),
                'queue_driver' => config('queue.default')
            ]);

            // Dočasne nastavíme admin emails ak sú poskytnuté
            if ($request->filled('admin_notification_emails')) {
                $adminEmails = $request->input('admin_notification_emails');
                config(['app.admin_notification_emails' => $adminEmails]);
                putenv('ADMIN_NOTIFICATION_EMAILS=' . $adminEmails);
                
                \Log::info('Admin notifications test - set temp config', [
                    'admin_emails' => $adminEmails
                ]);
            }

            // Získajme admin emaily pre test
            $adminEmails = get_admin_emails();
            \Log::info('Admin notifications test - resolved admin emails', [
                'admin_emails' => $adminEmails
            ]);

            if (empty($adminEmails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Žiadne admin email adresy nie sú nastavené. Zadajte admin emaily do poľa "Admin notifikácie".'
                ], 422);
            }

            // Pošleme testovú admin notifikáciu
            \Log::info('Admin notifications test - sending notification');
            notify_admins(
                'Test Admin Notifikácia',
                'Toto je testovacia admin notifikácia. Ak ste dostali tento email, admin notifikácie fungujú správne.',
                'info',
                'normal',
                route('admin.settings.index'),
                'Otvoriť nastavenia'
            );

            \Log::info('Admin notifications test - notification sent successfully');

            return response()->json([
                'success' => true,
                'message' => 'Testovacia admin notifikácia bola úspešne odoslaná na: ' . implode(', ', $adminEmails)
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin notifications test - error occurred', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri odosielaní admin notifikácie: ' . $e->getMessage()
            ], 422);
        }
    }

    public function diagnoseSMTP(Request $request)
    {
        $details = [];
        $errors = [];
        $warnings = [];

        try {
            // Získanie SMTP nastavení z formulára
            $host = $request->input('mail_host', env('MAIL_HOST'));
            $port = (int) $request->input('mail_port', env('MAIL_PORT'));
            $username = $request->input('mail_username', env('MAIL_USERNAME'));
            $password = $request->input('mail_password', env('MAIL_PASSWORD'));
            $encryption = $request->input('mail_encryption', env('MAIL_ENCRYPTION'));
            $fromAddress = $request->input('mail_from_address', env('MAIL_FROM_ADDRESS'));
            $fromName = $request->input('mail_from_name', env('MAIL_FROM_NAME'));

            $details[] = "🔍 SMTP Diagnostika pre {$host}:{$port}";
            $details[] = "────────────────────────────────────";

            // 1. Test DNS rozlíšenia
            $details[] = "\n1️⃣ Test DNS rozlíšenia:";
            $ip = gethostbyname($host);
            if ($ip !== $host) {
                $details[] = "   ✅ {$host} → {$ip}";
            } else {
                $errors[] = "DNS rozlíšenie zlyhalo";
                $details[] = "   ❌ Nemožno rozlíšiť {$host}";
            }

            // 2. Test portov
            $details[] = "\n2️⃣ Test dostupnosti portov:";
            $commonPorts = [25, 465, 587, 2525];
            $availablePorts = [];
            
            foreach ($commonPorts as $testPort) {
                $socket = @fsockopen($host, $testPort, $errno, $errstr, 5);
                if ($socket) {
                    $availablePorts[] = $testPort;
                    $status = ($testPort == $port) ? "✅ POUŽÍVANÝ" : "✅ dostupný";
                    $details[] = "   Port {$testPort}: {$status}";
                    fclose($socket);
                } else {
                    $status = ($testPort == $port) ? "❌ NEDOSTUPNÝ" : "❌ nedostupný";
                    $details[] = "   Port {$testPort}: {$status}";
                    if ($testPort == $port) {
                        $errors[] = "Zvolený port {$port} nie je dostupný";
                    }
                }
            }

            // 3. Odporúčania pre porty
            $details[] = "\n3️⃣ Informácie o portoch:";
            $details[] = "   Port 25:   SMTP (často blokovaný hostingami)";
            $details[] = "   Port 465:  SMTPS (SSL) - starší štandard";
            $details[] = "   Port 587:  SMTP (TLS) - odporúčaný";
            $details[] = "   Port 2525: Alternatívny SMTP";

            // 4. Test šifrovania
            $details[] = "\n4️⃣ Test šifrovania:";
            if ($encryption === 'ssl') {
                $details[] = "   📡 SSL šifrovanie (port 465)";
                if ($port !== 465) {
                    $warnings[] = "SSL sa zvyčajne používa s portom 465";
                }
            } elseif ($encryption === 'tls') {
                $details[] = "   🔒 TLS šifrovanie (port 587)";
                if ($port !== 587) {
                    $warnings[] = "TLS sa zvyčajne používa s portom 587";
                }
            } else {
                $details[] = "   ⚠️  Žiadne šifrovanie (nezabezpečené)";
                $warnings[] = "Odporúča sa používať TLS alebo SSL šifrovanie";
            }

            // 5. Kontrola používateľských údajov
            $details[] = "\n5️⃣ Kontrola autentifikácie:";
            if (empty($username)) {
                $errors[] = "SMTP používateľské meno nie je nastavené";
                $details[] = "   ❌ Používateľské meno: nie je nastavené";
            } else {
                $details[] = "   ✅ Používateľské meno: {$username}";
            }

            if (empty($password)) {
                $errors[] = "SMTP heslo nie je nastavené";
                $details[] = "   ❌ Heslo: nie je nastavené";
            } else {
                $details[] = "   ✅ Heslo: ***nastavené*** (" . strlen($password) . " znakov)";
            }

            // 6. Test from address
            $details[] = "\n6️⃣ Email odosielateľa:";
            if (empty($fromAddress)) {
                $errors[] = "Email adresa odosielateľa nie je nastavená";
                $details[] = "   ❌ From Address: nie je nastavené";
            } else {
                $details[] = "   ✅ From Address: {$fromAddress}";
                if (!filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email adresa odosielateľa nie je valídna";
                    $details[] = "   ❌ Nevalídny email formát";
                }
            }

            // 7. Odporúčania
            $details[] = "\n7️⃣ Odporúčania:";
            if (count($availablePorts) > 0) {
                $details[] = "   📍 Dostupné porty: " . implode(', ', $availablePorts);
                if (!in_array($port, $availablePorts)) {
                    $recommendedPort = in_array(587, $availablePorts) ? 587 : $availablePorts[0];
                    $details[] = "   💡 Odporúčame port: {$recommendedPort}";
                }
            }

            // 8. Záver diagnostiky
            $details[] = "\n8️⃣ Záver diagnostiky:";
            if (count($errors) === 0) {
                $details[] = "   ✅ Základná konfigurácia vyzerá v poriadku";
                $details[] = "   🔧 Môžete pokračovať testovaním emailu";
            } else {
                $details[] = "   ❌ Nájdené problémy: " . count($errors);
                $details[] = "   🔧 Opravte chyby a skúste znovu";
            }

            return response()->json([
                'success' => count($errors) === 0,
                'message' => count($errors) === 0 ? 
                    'Diagnostika úspešná - konfigurácia vyzerá v poriadku' : 
                    'Diagnostika našla ' . count($errors) . ' problémov',
                'details' => implode("\n", $details),
                'errors' => $errors,
                'warnings' => $warnings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri diagnostike SMTP: ' . $e->getMessage(),
                'details' => implode("\n", $details),
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function testExternalEmail(Request $request)
    {
        $request->validate([
            'external_email' => 'required|email',
        ]);

        $details = [];

        try {
            // Dynamicky nastavíme SMTP konfiguráciu z formulára
            $this->configureSMTPFromRequest($request);

            // Vyčistíme Laravel mail manager cache
            app('mail.manager')->purge();

            $externalEmail = $request->external_email;
            
            // Detekcia poskytovateľa emailu
            $provider = 'neznámy';
            if (strpos($externalEmail, '@gmail.com') !== false) {
                $provider = 'Gmail';
            } elseif (strpos($externalEmail, '@outlook.com') !== false || strpos($externalEmail, '@hotmail.com') !== false) {
                $provider = 'Outlook/Hotmail';
            } elseif (strpos($externalEmail, '@yahoo.com') !== false) {
                $provider = 'Yahoo';
            }

            $details[] = "📧 Test external email delivery";
            $details[] = "────────────────────────────────────";
            $details[] = "📤 Odosielateľ: " . config('mail.from.address');
            $details[] = "📥 Prijímateľ: {$externalEmail} ({$provider})";
            $details[] = "⏰ Čas: " . now()->format('d.m.Y H:i:s');

            // Pošleme detailný testovací email
            try {
                $emailContent = view('emails.external-test', [
                    'testTime' => now()->format('d.m.Y H:i:s'),
                    'fromAddress' => config('mail.from.address'),
                    'smtpHost' => config('mail.mailers.smtp.host'),
                    'smtpPort' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                ])->render();

                \Mail::html($emailContent, function ($message) use ($externalEmail) {
                    $message->to($externalEmail)
                            ->subject('🔧 SMTP Test z Erotikon.sk - ' . now()->format('d.m.Y H:i:s'))
                            ->priority(1); // Vysoká priorita
                });
            } catch (\Exception $viewException) {
                // Fallback na jednoduchý text ak view neexistuje
                \Mail::raw("🔧 SMTP Test z Erotikon.sk\n\n✅ Ak čítate túto správu, SMTP konfigurácia funguje správne!\n\nTestované: " . now()->format('d.m.Y H:i:s') . "\nOdosielateľ: " . config('mail.from.address') . "\nSMTP: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'), function ($message) use ($externalEmail) {
                    $message->to($externalEmail)
                            ->subject('🔧 SMTP Test z Erotikon.sk - ' . now()->format('d.m.Y H:i:s'));
                });
            }

            $details[] = "\n✅ Email úspešne odoslaný!";
            $details[] = "";
            $details[] = "🔍 Čo robiť ďalej:";
            $details[] = "1. Skontrolujte email za 1-2 minúty";
            $details[] = "2. Pozrite SPAM/Junk priečinok";
            $details[] = "3. Ak nepríde, problém je v hosting konfigurácii";
            $details[] = "4. Kontaktujte HostCreators support";

            return response()->json([
                'success' => true,
                'message' => 'External email test úspešne odoslaný na ' . $externalEmail,
                'details' => implode("\n", $details)
            ]);

        } catch (\Exception $e) {
            $details[] = "\n❌ Chyba pri odosielaní external emailu:";
            $details[] = "   " . $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri external email teste: ' . $e->getMessage(),
                'details' => implode("\n", $details)
            ], 422);
        }
    }

    /**
     * Dynamicky nastaví SMTP konfiguráciu z request parametrov
     */
    private function configureSMTPFromRequest(Request $request)
    {
        // Nastavíme SMTP konfiguráciu dynamicky
        $smtpConfig = [
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $request->input('mail_host', env('MAIL_HOST')),
            'mail.mailers.smtp.port' => (int) $request->input('mail_port', env('MAIL_PORT')),
            'mail.mailers.smtp.username' => $request->input('mail_username', env('MAIL_USERNAME')),
            'mail.mailers.smtp.password' => $request->input('mail_password', env('MAIL_PASSWORD')),
            'mail.mailers.smtp.encryption' => $request->input('mail_encryption', env('MAIL_ENCRYPTION')),
            'mail.from.address' => $request->input('mail_from_address', env('MAIL_FROM_ADDRESS')),
            'mail.from.name' => $request->input('mail_from_name', env('MAIL_FROM_NAME')),
            'queue.default' => 'sync', // KRITICKÉ: Použije synchronné odosielanie
        ];
        
        config($smtpConfig);
    }

    private function getEnvSettings()
    {
        // Načítame VŠETKY nastavenia z databázy s fallback na .env
        $dbSettings = \App\Models\Setting::getAllSettings();
        
        return [
            // Aplikačné nastavenia - tiež z databázy
            'app_name' => $dbSettings['app_name'] ?? env('APP_NAME', 'Erotikon'),
            'app_url' => $dbSettings['app_url'] ?? env('APP_URL', ''),
            
            // Stripe nastavenia
            'stripe_key' => $dbSettings['stripe_key'] ?? env('STRIPE_KEY', ''),
            'stripe_secret' => $dbSettings['stripe_secret'] ?? env('STRIPE_SECRET', ''),
            'stripe_webhook_secret' => $dbSettings['stripe_webhook_secret'] ?? env('STRIPE_WEBHOOK_SECRET', ''),
            
            // Google OAuth
            'google_client_id' => $dbSettings['google_client_id'] ?? env('GOOGLE_CLIENT_ID', ''),
            'google_client_secret' => $dbSettings['google_client_secret'] ?? env('GOOGLE_CLIENT_SECRET', ''),
            
            // Facebook OAuth  
            'facebook_client_id' => $dbSettings['facebook_client_id'] ?? env('FACEBOOK_CLIENT_ID', ''),
            'facebook_client_secret' => $dbSettings['facebook_client_secret'] ?? env('FACEBOOK_CLIENT_SECRET', ''),
            
            // Google Analytics & Tag Manager
            'google_analytics_id' => $dbSettings['google_analytics_id'] ?? env('GOOGLE_ANALYTICS_ID', ''),
            'google_tag_manager_id' => $dbSettings['google_tag_manager_id'] ?? env('GOOGLE_TAG_MANAGER_ID', ''),
            
            // Platobné nastavenia
            'payment_iban' => $dbSettings['payment_iban'] ?? env('PAYMENT_IBAN', ''),
            
            // SMS platby nastavenia
            'sms_payment_pid' => $dbSettings['sms_payment_pid'] ?? env('SMS_PAYMENT_PID', ''),
            'sms_payment_key' => $dbSettings['sms_payment_key'] ?? env('SMS_PAYMENT_KEY', ''),
            'sms_payment_url' => $dbSettings['sms_payment_url'] ?? env('SMS_PAYMENT_URL', 'https://pay.platbamobilom.sk/pay/'),
            
            // Nastavenia faktúr
            'invoice_company_name' => $dbSettings['invoice_company_name'] ?? env('INVOICE_COMPANY_NAME', ''),
            'invoice_company_address' => $dbSettings['invoice_company_address'] ?? env('INVOICE_COMPANY_ADDRESS', ''),
            'invoice_company_city' => $dbSettings['invoice_company_city'] ?? env('INVOICE_COMPANY_CITY', ''),
            'invoice_company_postal_code' => $dbSettings['invoice_company_postal_code'] ?? env('INVOICE_COMPANY_POSTAL_CODE', ''),
            'invoice_company_country' => $dbSettings['invoice_company_country'] ?? env('INVOICE_COMPANY_COUNTRY', ''),
            'invoice_company_ico' => $dbSettings['invoice_company_ico'] ?? env('INVOICE_COMPANY_ICO', ''),
            'invoice_company_dic' => $dbSettings['invoice_company_dic'] ?? env('INVOICE_COMPANY_DIC', ''),
            'invoice_company_ic_dph' => $dbSettings['invoice_company_ic_dph'] ?? env('INVOICE_COMPANY_IC_DPH', ''),
            'invoice_company_phone' => $dbSettings['invoice_company_phone'] ?? env('INVOICE_COMPANY_PHONE', ''),
            'invoice_company_email' => $dbSettings['invoice_company_email'] ?? env('INVOICE_COMPANY_EMAIL', ''),
            'invoice_company_website' => $dbSettings['invoice_company_website'] ?? env('INVOICE_COMPANY_WEBSITE', ''),
            'invoice_bank_name' => $dbSettings['invoice_bank_name'] ?? env('INVOICE_BANK_NAME', ''),
            'invoice_bank_account' => $dbSettings['invoice_bank_account'] ?? env('INVOICE_BANK_ACCOUNT', ''),
            'invoice_bank_iban' => $dbSettings['invoice_bank_iban'] ?? env('INVOICE_BANK_IBAN', ''),
            'invoice_bank_swift' => $dbSettings['invoice_bank_swift'] ?? env('INVOICE_BANK_SWIFT', ''),
            'invoice_constant_symbol' => $dbSettings['invoice_constant_symbol'] ?? env('INVOICE_CONSTANT_SYMBOL', ''),
            
            // Email nastavenia
            'mail_from_address' => $dbSettings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => $dbSettings['mail_from_name'] ?? env('MAIL_FROM_NAME', ''),
            
            // Admin notifikácie
            'admin_notification_emails' => $dbSettings['admin_notification_emails'] ?? env('ADMIN_NOTIFICATION_EMAILS', ''),
            
            // SMTP nastavenia
            'mail_host' => $dbSettings['mail_host'] ?? env('MAIL_HOST', ''),
            'mail_port' => $dbSettings['mail_port'] ?? env('MAIL_PORT', '587'),
            'mail_username' => $dbSettings['mail_username'] ?? env('MAIL_USERNAME', ''),
            'mail_password' => $dbSettings['mail_password'] ?? env('MAIL_PASSWORD', ''),
            'mail_encryption' => $dbSettings['mail_encryption'] ?? env('MAIL_ENCRYPTION', 'tls'),
        ];
    }

    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            throw new \Exception('.env súbor sa nenašiel');
        }
        
        $envContent = File::get($envPath);

        $updates = [
            'APP_NAME' => $data['app_name'] ?? env('APP_NAME'),
            'APP_URL' => $data['app_url'] ?? env('APP_URL'),
            'STRIPE_KEY' => $data['stripe_key'] ?? '',
            'STRIPE_SECRET' => $data['stripe_secret'] ?? '',
            'STRIPE_WEBHOOK_SECRET' => $data['stripe_webhook_secret'] ?? '',
            'GOOGLE_CLIENT_ID' => $data['google_client_id'] ?? '',
            'GOOGLE_CLIENT_SECRET' => $data['google_client_secret'] ?? '',
            'FACEBOOK_CLIENT_ID' => $data['facebook_client_id'] ?? '',
            'FACEBOOK_CLIENT_SECRET' => $data['facebook_client_secret'] ?? '',
            'GOOGLE_ANALYTICS_ID' => $data['google_analytics_id'] ?? '',
            'GOOGLE_TAG_MANAGER_ID' => $data['google_tag_manager_id'] ?? '',
            'PAYMENT_IBAN' => $data['payment_iban'] ?? '',
            'SMS_PAYMENT_PID' => $data['sms_payment_pid'] ?? '',
            'SMS_PAYMENT_KEY' => $data['sms_payment_key'] ?? '',
            'SMS_PAYMENT_URL' => $data['sms_payment_url'] ?? 'https://pay.platbamobilom.sk/pay/',
            'INVOICE_COMPANY_NAME' => $data['invoice_company_name'] ?? '',
            'INVOICE_COMPANY_ADDRESS' => $data['invoice_company_address'] ?? '',
            'INVOICE_COMPANY_CITY' => $data['invoice_company_city'] ?? '',
            'INVOICE_COMPANY_POSTAL_CODE' => $data['invoice_company_postal_code'] ?? '',
            'INVOICE_COMPANY_COUNTRY' => $data['invoice_company_country'] ?? '',
            'INVOICE_COMPANY_ICO' => $data['invoice_company_ico'] ?? '',
            'INVOICE_COMPANY_DIC' => $data['invoice_company_dic'] ?? '',
            'INVOICE_COMPANY_IC_DPH' => $data['invoice_company_ic_dph'] ?? '',
            'INVOICE_COMPANY_PHONE' => $data['invoice_company_phone'] ?? '',
            'INVOICE_COMPANY_EMAIL' => $data['invoice_company_email'] ?? '',
            'INVOICE_COMPANY_WEBSITE' => $data['invoice_company_website'] ?? '',
            'INVOICE_BANK_NAME' => $data['invoice_bank_name'] ?? '',
            'INVOICE_BANK_ACCOUNT' => $data['invoice_bank_account'] ?? '',
            'INVOICE_BANK_IBAN' => $data['invoice_bank_iban'] ?? '',
            'INVOICE_BANK_SWIFT' => $data['invoice_bank_swift'] ?? '',
            'INVOICE_CONSTANT_SYMBOL' => $data['invoice_constant_symbol'] ?? '',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
            'MAIL_FROM_NAME' => $data['mail_from_name'] ?? '',
            'ADMIN_NOTIFICATION_EMAILS' => $data['admin_notification_emails'] ?? '',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $data['mail_encryption'] ?? '',
        ];

        foreach ($updates as $key => $value) {
            // Escapovanie hodnôt s medzerami alebo špeciálnymi znakmi
            $escapedValue = $this->escapeEnvValue($value);
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$escapedValue}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                // Pridanie novej premennej na koniec súboru
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envPath, $envContent);
    }
    
    private function escapeEnvValue($value)
    {
        if (empty($value)) {
            return '';
        }
        
        // Ak hodnota obsahuje medzery alebo špeciálne znaky, obalíme ju do úvodzoviek
        if (preg_match('/\s|[#"\'\\\\]/', $value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }
        
        return $value;
    }

    /**
     * Aktualizuje VŠETKY nastavenia v databáze
     */
    private function updateDatabaseSettings($data)
    {
        $settingsToUpdate = [
            // Aplikačné nastavenia - tiež do databázy
            'app_name' => $data['app_name'] ?? 'Erotikon',
            'app_url' => $data['app_url'] ?? '',
            
            // Stripe nastavenia
            'stripe_key' => $data['stripe_key'] ?? '',
            'stripe_secret' => $data['stripe_secret'] ?? '',
            'stripe_webhook_secret' => $data['stripe_webhook_secret'] ?? '',
            
            // Google OAuth
            'google_client_id' => $data['google_client_id'] ?? '',
            'google_client_secret' => $data['google_client_secret'] ?? '',
            
            // Facebook OAuth
            'facebook_client_id' => $data['facebook_client_id'] ?? '',
            'facebook_client_secret' => $data['facebook_client_secret'] ?? '',
            
            // Google Analytics & Tag Manager
            'google_analytics_id' => $data['google_analytics_id'] ?? '',
            'google_tag_manager_id' => $data['google_tag_manager_id'] ?? '',
            
            // Platobné nastavenia
            'payment_iban' => $data['payment_iban'] ?? '',
            
            // SMS platby nastavenia
            'sms_payment_pid' => $data['sms_payment_pid'] ?? '',
            'sms_payment_key' => $data['sms_payment_key'] ?? '',
            'sms_payment_url' => $data['sms_payment_url'] ?? 'https://pay.platbamobilom.sk/pay/',
            
            // Nastavenia faktúr
            'invoice_company_name' => $data['invoice_company_name'] ?? '',
            'invoice_company_address' => $data['invoice_company_address'] ?? '',
            'invoice_company_city' => $data['invoice_company_city'] ?? '',
            'invoice_company_postal_code' => $data['invoice_company_postal_code'] ?? '',
            'invoice_company_country' => $data['invoice_company_country'] ?? '',
            'invoice_company_ico' => $data['invoice_company_ico'] ?? '',
            'invoice_company_dic' => $data['invoice_company_dic'] ?? '',
            'invoice_company_ic_dph' => $data['invoice_company_ic_dph'] ?? '',
            'invoice_company_phone' => $data['invoice_company_phone'] ?? '',
            'invoice_company_email' => $data['invoice_company_email'] ?? '',
            'invoice_company_website' => $data['invoice_company_website'] ?? '',
            'invoice_bank_name' => $data['invoice_bank_name'] ?? '',
            'invoice_bank_account' => $data['invoice_bank_account'] ?? '',
            'invoice_bank_iban' => $data['invoice_bank_iban'] ?? '',
            'invoice_bank_swift' => $data['invoice_bank_swift'] ?? '',
            'invoice_constant_symbol' => $data['invoice_constant_symbol'] ?? '',
            
            // Email nastavenia
            'mail_from_address' => $data['mail_from_address'] ?? '',
            'mail_from_name' => $data['mail_from_name'] ?? '',
            
            // Admin notifikácie
            'admin_notification_emails' => $data['admin_notification_emails'] ?? '',
            
            // SMTP nastavenia
            'mail_host' => $data['mail_host'] ?? '',
            'mail_port' => $data['mail_port'] ?? '587',
            'mail_username' => $data['mail_username'] ?? '',
            'mail_password' => $data['mail_password'] ?? '',
            'mail_encryption' => $data['mail_encryption'] ?? 'tls',
        ];

        foreach ($settingsToUpdate as $key => $value) {
            \App\Models\Setting::set($key, $value);
        }
        
        // ODSTRÁNENÉ: Už neukladáme nič do .env súboru, všetko ide do databázy
        // Pre APP_NAME a APP_URL môžeme vytvoriť symbolické linky ak treba
        // ale všetko bude v databáze pre jednoduchšiu správu
    }
} 