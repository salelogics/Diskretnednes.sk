<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixSharedHosting extends Command
{
    protected $signature = 'fix:shared-hosting {--check : Len skontroluj stav bez opráv}';
    protected $description = 'Oprava problémov s obrázkami na shared hostingu';

    public function handle()
    {
        $checkOnly = $this->option('check');
        
        $this->info('🔧 Diagnostika shared hosting problémov...');
        $this->line('');
        
        // 1. Detekcia shared hosting štruktúry
        $this->checkSharedHostingStructure($checkOnly);
        
        // 2. Kontrola symbolic linkov
        $this->checkSymbolicLinks($checkOnly);
        
        // 3. Kontrola .htaccess
        $this->checkHtaccess($checkOnly);
        
        // 4. Test konkrétnych súborov
        $this->testImageAccess();
        
        $this->line('');
        $this->info('✅ Diagnostika dokončená!');
        
        if ($checkOnly) {
            $this->line('');
            $this->info('💡 Pre opravu problémov spustite: php artisan fix:shared-hosting');
        }
    }
    
    private function checkSharedHostingStructure($checkOnly)
    {
        $this->info('1. 🔍 Kontrola shared hosting štruktúry...');
        
        // Možné cesty na shared hostingu
        $possiblePaths = [
            '/web/shared/storage/app/public',
            '/shared/storage/app/public', 
            '/public_html/shared/storage/app/public',
            '/www/shared/storage/app/public',
            base_path('../shared/storage/app/public'),
            base_path('../../shared/storage/app/public'),
        ];
        
        $foundPath = null;
        
        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                $foundPath = $path;
                $this->line("  ✅ Našiel som shared storage: $path");
                break;
            }
        }
        
        if (!$foundPath) {
            // Skús relatívne cesty
            $basePath = base_path();
            $parentDir = dirname($basePath);
            $grandParentDir = dirname($parentDir);
            
            $relativePaths = [
                $parentDir . '/shared/storage/app/public',
                $grandParentDir . '/shared/storage/app/public',
            ];
            
            foreach ($relativePaths as $path) {
                if (File::exists($path)) {
                    $foundPath = $path;
                    $this->line("  ✅ Našiel som shared storage: $path");
                    break;
                }
            }
        }
        
        if ($foundPath) {
            // Skontroluj či tam sú naše súbory
            $verificationPath = $foundPath . '/ads/verification';
            if (File::exists($verificationPath)) {
                $fileCount = count(File::glob($verificationPath . '/verification_*.jpg'));
                $this->line("  ✅ Verification priečinok existuje s $fileCount súbormi");
                
                if (!$checkOnly) {
                    // Aktualizuj .env súbor
                    $this->updateEnvFile($foundPath);
                }
            } else {
                $this->error("  ❌ Verification priečinok neexistuje v $foundPath");
            }
        } else {
            $this->error('  ❌ Nenašiel som shared storage štruktúru');
            $this->line('     Skontrolujte manuálne kde sa nachádzajú súbory na FTP');
        }
        
        return $foundPath;
    }
    
    private function updateEnvFile($sharedPath)
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('  ❌ .env súbor neexistuje');
            return;
        }
        
        $envContent = File::get($envPath);
        
        // Pridaj alebo aktualizuj SHARED_STORAGE_PATH
        if (strpos($envContent, 'SHARED_STORAGE_PATH=') !== false) {
            $envContent = preg_replace(
                '/SHARED_STORAGE_PATH=.*/',
                'SHARED_STORAGE_PATH=' . $sharedPath,
                $envContent
            );
        } else {
            $envContent .= "\n# Shared Hosting Configuration\nSHARED_STORAGE_PATH=" . $sharedPath . "\n";
        }
        
        File::put($envPath, $envContent);
        $this->line("  ✅ Aktualizovaný .env súbor s SHARED_STORAGE_PATH=$sharedPath");
    }
    
    private function checkSymbolicLinks($checkOnly)
    {
        $this->info('2. 🔗 Kontrola symbolic linkov...');
        
        $publicStorage = public_path('storage');
        
        if (File::exists($publicStorage)) {
            if (is_link($publicStorage)) {
                $target = readlink($publicStorage);
                $this->line("  ✅ Symbolic link existuje: $publicStorage -> $target");
            } else {
                $this->line("  ⚠️  Storage cesta existuje ale nie je symbolic link");
                
                if (!$checkOnly) {
                    // Odstráň a vytvor nový link
                    File::deleteDirectory($publicStorage);
                    $this->call('storage:link');
                    $this->line("  ✅ Vytvorený nový symbolic link");
                }
            }
        } else {
            $this->error("  ❌ Public storage neexistuje");
            
            if (!$checkOnly) {
                $this->call('storage:link');
                $this->line("  ✅ Vytvorený symbolic link");
            }
        }
    }
    
    private function checkHtaccess($checkOnly)
    {
        $this->info('3. 📄 Kontrola .htaccess...');
        
        $htaccessPath = public_path('.htaccess');
        
        if (!File::exists($htaccessPath)) {
            $this->error("  ❌ .htaccess súbor neexistuje v public/");
            
            if (!$checkOnly) {
                $this->createHtaccess($htaccessPath);
            }
        } else {
            $content = File::get($htaccessPath);
            
            // Skontroluj či obsahuje pravidlá pre storage
            if (strpos($content, 'storage') === false) {
                $this->line("  ⚠️  .htaccess neobsahuje pravidlá pre storage");
                
                if (!$checkOnly) {
                    $this->updateHtaccess($htaccessPath);
                }
            } else {
                $this->line("  ✅ .htaccess obsahuje storage pravidlá");
            }
        }
    }
    
    private function createHtaccess($path)
    {
        $htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# File access security
<FilesMatch "\.(env|log|sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>
HTACCESS;

        File::put($path, $htaccessContent);
        $this->line("  ✅ Vytvorený .htaccess súbor");
    }
    
    private function updateHtaccess($path)
    {
        $content = File::get($path);
        
        // Pridaj storage pravidlá ak chýbajú
        $storageRules = "\n# Storage access rules\n<IfModule mod_alias.c>\n    Alias /storage " . storage_path('app/public') . "\n</IfModule>\n";
        
        $content .= $storageRules;
        File::put($path, $content);
        
        $this->line("  ✅ Aktualizovaný .htaccess s storage pravidlami");
    }
    
    private function testImageAccess()
    {
        $this->info('4. 🖼️  Test prístupu k obrázkom...');
        
        $testFiles = [
            'verification_50773.jpg',
            'verification_50758.jpg', 
            'verification_50841.jpg'
        ];
        
        foreach ($testFiles as $filename) {
            // Test lokálnej cesty
            $localPath = storage_path('app/public/ads/verification/' . $filename);
            $sharedPath = env('SHARED_STORAGE_PATH', storage_path('app/public')) . '/ads/verification/' . $filename;
            
            if (File::exists($localPath)) {
                $this->line("  ✅ $filename existuje lokálne");
            } elseif (File::exists($sharedPath)) {
                $this->line("  ✅ $filename existuje v shared storage");
            } else {
                $this->error("  ❌ $filename sa nenašiel");
            }
        }
    }
} 