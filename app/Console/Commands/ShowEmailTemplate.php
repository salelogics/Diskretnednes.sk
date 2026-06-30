<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;

class ShowEmailTemplate extends Command
{
    protected $signature = 'show:email-template {key}';
    protected $description = 'Zobraz obsah email templatu';

    public function handle()
    {
        $key = $this->argument('key');
        
        $template = EmailTemplate::where('key', $key)->first();
        
        if (!$template) {
            $this->error("❌ Template s kľúčom '{$key}' sa nenašiel!");
            return 1;
        }
        
        $this->info("📧 TEMPLATE: {$template->name}");
        $this->line("Key: {$template->key}");
        $this->line("Type: {$template->type}");
        $this->line("Subject: {$template->subject}");
        $this->line("Created: {$template->created_at}");
        $this->line("Updated: {$template->updated_at}");
        $this->line('');
        
        $this->info('📄 CONTENT (prvých 500 znakov):');
        $this->line('=====================================');
        
        $content = substr($template->content, 0, 500);
        $this->line($content);
        
        if (strlen($template->content) > 500) {
            $this->line('...(skrátené)');
        }
        
        // Skontroluj či obsahuje moderný dizajn
        $hasModernDesign = strpos($template->content, 'gradient') !== false || 
                          strpos($template->content, 'border-radius') !== false ||
                          strpos($template->content, 'box-shadow') !== false;
        
        $this->line('');
        $designStatus = $hasModernDesign ? '✅ Moderný dizajn' : '❌ Starý jednoduchý dizajn';
        $this->line("🎨 Dizajn: {$designStatus}");
        
        return 0;
    }
} 