<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\File;

class UpdateEmailTemplate extends Command
{
    protected $signature = 'email:update-template {key}';
    protected $description = 'Update email template from blade file';

    public function handle()
    {
        $key = $this->argument('key');
        
        if ($key === 'ad_created') {
            $content = File::get('resources/views/emails/new-ad-created.blade.php');
            
            EmailTemplate::updateOrCreate(
                ['key' => 'ad_created'],
                [
                    'name' => 'Email pre vytvorenie inzerátu',
                    'type' => 'user',
                    'subject' => 'Váš inzerát #{{ad_id}} bol vytvorený - Erotikon.sk',
                    'content' => $content,
                    'variables' => json_encode([
                        'user_name' => 'Meno používateľa',
                        'user_email' => 'Email používateľa',
                        'ad_id' => 'ID inzerátu',
                        'ad_nickname' => 'Prezývka v inzeráte', 
                        'ad_type' => 'Typ inzerátu',
                        'ad_city' => 'Mesto',
                        'ad_views' => 'Počet zobrazení',
                        'expiry_date' => 'Dátum expirácie',
                        'days_left' => 'Počet dní do expirácie',
                        'package_price' => 'Cena balíčka',
                        'payment_id' => 'ID platby',
                        'amount' => 'Suma',
                        'due_date' => 'Dátum splatnosti'
                    ]),
                    'is_active' => true
                ]
            );
            
            $this->info('Email template ad_created bol úspešne aktualizovaný!');
        } else {
            $this->error('Neznámy template key: ' . $key);
        }
    }
}