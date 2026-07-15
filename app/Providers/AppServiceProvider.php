<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Načítaj nastavenia z databázy do config (len ak databáza existuje)
        try {
            if (Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::getAllSettings();
                
                // Dynamicky nastav Stripe config z databázy
                if (isset($settings['stripe_key'])) {
                    config(['services.stripe.key' => $settings['stripe_key']]);
                }
                if (isset($settings['stripe_secret'])) {
                    config(['services.stripe.secret' => $settings['stripe_secret']]);
                }
                if (isset($settings['stripe_webhook_secret'])) {
                    config(['services.stripe.webhook_secret' => $settings['stripe_webhook_secret']]);
                }
                
                // Nastav Google OAuth nastavenia z databázy
                if (isset($settings['google_client_id'])) {
                    config(['services.google.client_id' => $settings['google_client_id']]);
                }
                if (isset($settings['google_client_secret'])) {
                    config(['services.google.client_secret' => $settings['google_client_secret']]);
                }
                
                // Nastav SMTP nastavenia z databázy a mail driver na SMTP
                if (isset($settings['mail_host']) && !empty($settings['mail_host'])) {
                    config(['mail.default' => 'smtp']);
                    config(['mail.mailers.smtp.host' => $settings['mail_host']]);
                }
                if (isset($settings['mail_port'])) {
                    config(['mail.mailers.smtp.port' => $settings['mail_port']]);
                }
                if (isset($settings['mail_username'])) {
                    config(['mail.mailers.smtp.username' => $settings['mail_username']]);
                    config(['mail.from.address' => $settings['mail_username']]);
                }
                if (isset($settings['mail_password'])) {
                    config(['mail.mailers.smtp.password' => $settings['mail_password']]);
                }
                if (isset($settings['mail_encryption'])) {
                    config(['mail.mailers.smtp.encryption' => $settings['mail_encryption']]);
                }
                
                // Nastav Facebook OAuth nastavenia z databázy
                if (isset($settings['facebook_client_id'])) {
                    config(['services.facebook.client_id' => $settings['facebook_client_id']]);
                }
                if (isset($settings['facebook_client_secret'])) {
                    config(['services.facebook.client_secret' => $settings['facebook_client_secret']]);
                }
                
                // Dynamicky nastav redirect URLs
                $appUrl = $settings['app_url'] ?? env('APP_URL', 'https://diskretnednes.sk');
                config(['services.google.redirect' => $appUrl . '/auth/google/callback']);
                config(['services.facebook.redirect' => $appUrl . '/auth/facebook/callback']);

                // SEO defaulty z databázy (ak existujú)
                if (isset($settings['seo_default_title'])) {
                    config(['seo.default_title' => $settings['seo_default_title']]);
                }
                if (isset($settings['seo_default_description'])) {
                    config(['seo.default_description' => $settings['seo_default_description']]);
                }
                if (isset($settings['seo_default_image'])) {
                    config(['seo.default_image' => $settings['seo_default_image']]);
                }
            }

            // Predvolené SEO z configu (fallback pre všetky layouty)
            $seoConfig = config('seo');
            if (is_array($seoConfig)) {
                config([
                    'seo.default_title' => config('seo.default_title', config('app.name') . ' - Erotické služby a inzeráty pre dospelých'),
                    'seo.default_description' => config('seo.default_description', 'Objavte najlepšie erotické služby, tantra masáže a exkluzívne kluby. Bezpečná platforma pre dospelých s overenými inzerátmi.'),
                    'seo.default_image' => config('seo.default_image', 'images/uploads/diskretne-dnes-logo-black.png'),
                ]);
            }
        } catch (\Exception $e) {
            // Ticho ignoruj chyby (napr. počas migrácie keď tabuľka ešte neexistuje)
            logger()->debug('Could not load settings from database: ' . $e->getMessage());
        }
    }
}
