<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Ad;
use App\Models\AdReport;

class TestFixedEmailTemplates extends Command
{
    protected $signature = 'test:fixed-email-templates';
    protected $description = 'Test opravených HTML email templatov';

    public function handle()
    {
        $this->info('🔧 Test opravených HTML email templatov');
        $this->line('=======================================');
        
        $notificationService = app(NotificationService::class);
        
        // 1. Test kontaktný formulár
        $this->info('1. Testujem kontaktný formulár (HTML render)...');
        try {
            $notificationService->sendContactFormEmail([
                'name' => 'Test HTML Render',
                'email' => 'test@example.com',
                'subject' => 'TEST: HTML templaty opravené',
                'message' => 'Tento email testuje či sa HTML templaty správne renderujú s gradientmi, farbami a moderným dizajnom po oprave Mail::html() namiesto Mail::raw().'
            ]);
            $this->info('✅ Kontaktný formulár - HTML email odoslaný');
        } catch (\Exception $e) {
            $this->error('❌ Kontaktný formulár - chyba: ' . $e->getMessage());
        }
        
        // 2. Test nahlásenie inzerátu
        $this->info('2. Testujem nahlásenie inzerátu (HTML render)...');
        try {
            $ad = Ad::first();
            if ($ad) {
                $report = AdReport::create([
                    'ad_id' => $ad->id,
                    'reason' => 'TEST: HTML templaty opravené',
                    'details' => 'Test po oprave Mail::raw() na Mail::html() - email by mal mať červený gradient, shadow efekty a profesionálny dizajn.',
                    'reporter_email' => 'test@example.com',
                    'reporter_ip' => '127.0.0.1',
                    'status' => 'pending'
                ]);
                
                $notificationService->newAdReport($report);
                $this->info('✅ Nahlásenie inzerátu - HTML email odoslaný');
            } else {
                $this->warn('⚠️ Žiadny inzerát pre test nahlásenia');
            }
        } catch (\Exception $e) {
            $this->error('❌ Nahlásenie inzerátu - chyba: ' . $e->getMessage());
        }
        
        $this->line('');
        $this->info('🎯 Test dokončený!');
        $this->line('   → Skontroluj emaily na: info@diskretnednes.sk');
        $this->line('   → Emaily by MALI mať pekný HTML dizajn s gradientmi!');
        $this->line('   → Ak sú stále jednoduché, problém je inde');
        
        return 0;
    }
} 