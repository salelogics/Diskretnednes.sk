<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pridáme chýbajúce nastavenia ktoré sa predtým ukladali len do .env
        $missingSettings = [
            // Aplikačné nastavenia
            'app_name' => [
                'value' => env('APP_NAME', 'Erotikon'),
                'description' => 'Názov aplikácie',
                'type' => 'string'
            ],
            'app_url' => [
                'value' => env('APP_URL', 'https://erotikon.sk'),
                'description' => 'URL aplikácie',
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

            // Platobné nastavenia
            'payment_iban' => [
                'value' => env('PAYMENT_IBAN', ''),
                'description' => 'IBAN pre platby',
                'type' => 'string'
            ],

            // SMS platby URL
            'sms_payment_url' => [
                'value' => env('SMS_PAYMENT_URL', 'https://pay.platbamobilom.sk/pay/'),
                'description' => 'SMS Payment URL',
                'type' => 'string'
            ],

            // Rozšírené nastavenia faktúr
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
                'value' => env('INVOICE_COMPANY_COUNTRY', 'Slovensko'),
                'description' => 'Krajina firmy na faktúrach',
                'type' => 'string'
            ],
            'invoice_company_website' => [
                'value' => env('INVOICE_COMPANY_WEBSITE', 'https://erotikon.sk'),
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
                'value' => env('MAIL_FROM_ADDRESS', 'noreply@erotikon.sk'),
                'description' => 'Email adresa odosielateľa',
                'type' => 'string'
            ],
            'mail_from_name' => [
                'value' => env('MAIL_FROM_NAME', 'Erotikon'),
                'description' => 'Meno odosielateľa',
                'type' => 'string'
            ],
        ];

        // Pridáme len tie nastavenia ktoré ešte neexistujú
        foreach ($missingSettings as $key => $config) {
            $exists = DB::table('settings')->where('key', $key)->exists();
            
            if (!$exists) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Odstránime pridané nastavenia
        $keysToRemove = [
            'app_name', 'app_url', 'google_client_id', 'google_client_secret',
            'facebook_client_id', 'facebook_client_secret', 'payment_iban',
            'sms_payment_url', 'invoice_company_city', 'invoice_company_postal_code',
            'invoice_company_country', 'invoice_company_website', 'invoice_bank_name',
            'invoice_bank_account', 'invoice_bank_iban', 'invoice_bank_swift',
            'invoice_constant_symbol', 'mail_from_address', 'mail_from_name'
        ];

        DB::table('settings')->whereIn('key', $keysToRemove)->delete();
    }
};
