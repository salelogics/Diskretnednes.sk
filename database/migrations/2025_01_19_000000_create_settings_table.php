<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['string', 'number', 'boolean', 'json', 'text'])->default('string');
            $table->timestamps();

            $table->index('key');
        });

        // Prenesieme základné nastavenia z .env do databázy
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Naplní základné nastavenia
     */
    private function seedDefaultSettings(): void
    {
        $defaultSettings = [
            // Aplikačné nastavenia
            'app_name' => [
                'value' => env('APP_NAME', 'Diskrétne Dnes'),
                'description' => 'Názov aplikácie',
                'type' => 'string'
            ],
            'app_url' => [
                'value' => env('APP_URL', ''),
                'description' => 'URL aplikácie',
                'type' => 'string'
            ],
            
            // Stripe nastavenia
            'stripe_key' => [
                'value' => env('STRIPE_KEY', ''),
                'description' => 'Stripe Public Key',
                'type' => 'string'
            ],
            'stripe_secret' => [
                'value' => env('STRIPE_SECRET', ''),
                'description' => 'Stripe Secret Key', 
                'type' => 'string'
            ],
            'stripe_webhook_secret' => [
                'value' => env('STRIPE_WEBHOOK_SECRET', ''),
                'description' => 'Stripe Webhook Secret',
                'type' => 'string'
            ],

            // Google OAuth
            'google_client_id' => [
                'value' => env('GOOGLE_CLIENT_ID', ''),
                'description' => 'Google OAuth Client ID',
                'type' => 'string'
            ],
            'google_client_secret' => [
                'value' => env('GOOGLE_CLIENT_SECRET', ''),
                'description' => 'Google OAuth Client Secret',
                'type' => 'string'
            ],

            // Facebook OAuth
            'facebook_client_id' => [
                'value' => env('FACEBOOK_CLIENT_ID', ''),
                'description' => 'Facebook OAuth Client ID',
                'type' => 'string'
            ],
            'facebook_client_secret' => [
                'value' => env('FACEBOOK_CLIENT_SECRET', ''),
                'description' => 'Facebook OAuth Client Secret',
                'type' => 'string'
            ],

            // Google Analytics & Tag Manager
            'google_analytics_id' => [
                'value' => env('GOOGLE_ANALYTICS_ID', ''),
                'description' => 'Google Analytics ID',
                'type' => 'string'
            ],
            'google_tag_manager_id' => [
                'value' => env('GOOGLE_TAG_MANAGER_ID', ''),
                'description' => 'Google Tag Manager ID',
                'type' => 'string'
            ],

            // Platobné nastavenia
            'payment_iban' => [
                'value' => env('PAYMENT_IBAN', ''),
                'description' => 'IBAN pre platby',
                'type' => 'string'
            ],

            // SMS platby nastavenia
            'sms_payment_pid' => [
                'value' => env('SMS_PAYMENT_PID', ''),
                'description' => 'SMS Payment PID',
                'type' => 'string'
            ],
            'sms_payment_key' => [
                'value' => env('SMS_PAYMENT_KEY', ''),
                'description' => 'SMS Payment Key',
                'type' => 'string'
            ],
            'sms_payment_url' => [
                'value' => env('SMS_PAYMENT_URL', 'https://pay.platbamobilom.sk/pay/'),
                'description' => 'SMS Payment URL',
                'type' => 'string'
            ],

            // Nastavenia faktúr
            'invoice_company_name' => [
                'value' => env('INVOICE_COMPANY_NAME', ''),
                'description' => 'Názov firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_address' => [
                'value' => env('INVOICE_COMPANY_ADDRESS', ''),
                'description' => 'Adresa firmy na faktúrach',
                'type' => 'text'
            ],
            'invoice_company_city' => [
                'value' => env('INVOICE_COMPANY_CITY', ''),
                'description' => 'Mesto firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_postal_code' => [
                'value' => env('INVOICE_COMPANY_POSTAL_CODE', ''),
                'description' => 'PSČ firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_country' => [
                'value' => env('INVOICE_COMPANY_COUNTRY', ''),
                'description' => 'Krajina firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_ico' => [
                'value' => env('INVOICE_COMPANY_ICO', ''),
                'description' => 'IČO firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_dic' => [
                'value' => env('INVOICE_COMPANY_DIC', ''),
                'description' => 'DIČ firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_ic_dph' => [
                'value' => env('INVOICE_COMPANY_IC_DPH', ''),
                'description' => 'IČ DPH firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_phone' => [
                'value' => env('INVOICE_COMPANY_PHONE', ''),
                'description' => 'Telefón firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_email' => [
                'value' => env('INVOICE_COMPANY_EMAIL', ''),
                'description' => 'Email firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_website' => [
                'value' => env('INVOICE_COMPANY_WEBSITE', ''),
                'description' => 'Webstránka firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_bank_name' => [
                'value' => env('INVOICE_BANK_NAME', ''),
                'description' => 'Názov banky na faktúrach',
                'type' => 'string'
            ],
            'invoice_bank_account' => [
                'value' => env('INVOICE_BANK_ACCOUNT', ''),
                'description' => 'Číslo účtu na faktúrach',
                'type' => 'string'
            ],
            'invoice_bank_iban' => [
                'value' => env('INVOICE_BANK_IBAN', ''),
                'description' => 'IBAN na faktúrach',
                'type' => 'string'
            ],
            'invoice_bank_swift' => [
                'value' => env('INVOICE_BANK_SWIFT', ''),
                'description' => 'SWIFT na faktúrach',
                'type' => 'string'
            ],
            'invoice_constant_symbol' => [
                'value' => env('INVOICE_CONSTANT_SYMBOL', ''),
                'description' => 'Konštantný symbol na faktúrach',
                'type' => 'string'
            ],

            // Email nastavenia
            'mail_from_address' => [
                'value' => env('MAIL_FROM_ADDRESS', ''),
                'description' => 'Email adresa odosielateľa',
                'type' => 'string'
            ],
            'mail_from_name' => [
                'value' => env('MAIL_FROM_NAME', ''),
                'description' => 'Meno odosielateľa',
                'type' => 'string'
            ],

            // Admin notifikácie
            'admin_notification_emails' => [
                'value' => env('ADMIN_NOTIFICATION_EMAILS', 'admin@diskretnednes.sk'),
                'description' => 'Admin Notification Emails',
                'type' => 'string'
            ],

            // SMTP nastavenia
            'mail_host' => [
                'value' => env('MAIL_HOST', ''),
                'description' => 'SMTP Host',
                'type' => 'string'
            ],
            'mail_port' => [
                'value' => env('MAIL_PORT', '587'),
                'description' => 'SMTP Port',
                'type' => 'number'
            ],
            'mail_username' => [
                'value' => env('MAIL_USERNAME', ''),
                'description' => 'SMTP Username',
                'type' => 'string'
            ],
            'mail_password' => [
                'value' => env('MAIL_PASSWORD', ''),
                'description' => 'SMTP Password',
                'type' => 'string'
            ],
            'mail_encryption' => [
                'value' => env('MAIL_ENCRYPTION', 'tls'),
                'description' => 'SMTP Encryption',
                'type' => 'string'
            ],
        ];

        foreach ($defaultSettings as $key => $config) {
            DB::table('settings')->insert([
                'key' => $key,
                'value' => $config['value'],
                'description' => $config['description'],
                'type' => $config['type'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}; 