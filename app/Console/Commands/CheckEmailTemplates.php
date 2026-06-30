<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;

class CheckEmailTemplates extends Command
{
    protected $signature = 'check:email-templates';
    protected $description = 'Skontroluj email templaty v databáze';

    public function handle()
    {
        $this->info('📧 EMAIL TEMPLATES V DATABÁZE:');
        $this->line('===============================');
        
        $templates = EmailTemplate::all(['id', 'key', 'name', 'type', 'created_at', 'updated_at']);
        
        if ($templates->isEmpty()) {
            $this->warn('❌ Žiadne email templaty v databáze!');
        } else {
            foreach ($templates as $template) {
                $this->line("ID: {$template->id} | Key: {$template->key}");
                $this->line("   Name: {$template->name} ({$template->type})");
                $this->line("   Created: {$template->created_at}");
                $this->line("   Updated: {$template->updated_at}");
                $this->line('   ---');
            }
        }
        
        $this->line('');
        $this->info('🔍 HĽADANÉ KĽÚČE:');
        $expected = ['contact', 'ad_report', 'ad_created', 'admin_new_ad'];
        foreach ($expected as $key) {
            $exists = $templates->where('key', $key)->first();
            $status = $exists ? '✅' : '❌';
            $this->line("{$status} {$key}");
        }
        
        return 0;
    }
} 