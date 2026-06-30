<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseRestore extends Command
{
    protected $signature = 'db:restore {backup?} {--force}';
    protected $description = 'Obnoví databázu zo zálohy';

    public function handle()
    {
        $this->info('🔄 Začínam obnovenie databázy...');

        $backupDir = storage_path('app/backups');
        
        // Ak nie je zadaný backup súbor, zobraz dostupné
        $backupFile = $this->argument('backup');
        
        if (!$backupFile) {
            $this->showAvailableBackups($backupDir);
            $backupFile = $this->ask('Zadajte názov backup súboru (alebo "latest" pre najnovší)');
        }

        // Ak je zadané "latest", nájdi najnovší backup
        if ($backupFile === 'latest') {
            $backupFile = $this->getLatestBackup($backupDir);
            if (!$backupFile) {
                $this->error('❌ Žiadne zálohy nenájdené!');
                return 1;
            }
            $this->info("📁 Používam najnovšiu zálohu: " . basename($backupFile));
        } else {
            // Ak nie je zadaná plná cesta, predpokladaj že je v backup priečinku
            if (!str_contains($backupFile, '/')) {
                $backupFile = $backupDir . '/' . $backupFile;
            }
        }

        // Kontrola existencie súboru
        if (!file_exists($backupFile)) {
            $this->error("❌ Súbor {$backupFile} neexistuje!");
            return 1;
        }

        $this->info("📁 Obnovujem z: " . basename($backupFile));

        // Potvrdenie od používateľa
        if (!$this->option('force')) {
            $database = config('database.connections.' . config('database.default'))['database'];
            $this->warn("⚠️  POZOR: Toto prepíše všetky údaje v databáze {$database}!");
            
            if (!$this->confirm('Pokračovať?')) {
                $this->info('❌ Obnovenie zrušené.');
                return 1;
            }
        }

        // Získaj database config
        $config = config('database.connections.' . config('database.default'));
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // Priprav SQL súbor
        $sqlFile = $backupFile;
        $tempFile = null;

        // Ak je súbor kompresovaný, dekompresuj ho
        if (str_ends_with($backupFile, '.gz')) {
            $this->info('🗜️ Dekompresovanie zálohy...');
            $tempFile = sys_get_temp_dir() . '/restore_temp_' . time() . '.sql';
            
            $command = "gunzip -c " . escapeshellarg($backupFile) . " > " . escapeshellarg($tempFile);
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                $this->error('❌ Chyba pri dekompresovaní!');
                return 1;
            }
            
            $sqlFile = $tempFile;
        }

        // Obnovenie databázy
        $this->info('🔄 Obnovujem databázu...');
        
        $command = sprintf(
            'mysql -h%s -P%s -u%s -p%s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info('✅ Databáza úspešne obnovená!');
            
            // Vyčisti Laravel cache
            $this->info('🧹 Čistím Laravel cache...');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            
            $this->info('🎉 Obnovenie dokončené!');
        } else {
            $this->error('❌ Chyba pri obnovovaní databázy!');
            return 1;
        }

        // Vyčisti dočasný súbor
        if ($tempFile && file_exists($tempFile)) {
            unlink($tempFile);
        }

        return 0;
    }

    private function showAvailableBackups($backupDir)
    {
        $this->info('📋 Dostupné zálohy:');
        
        if (!is_dir($backupDir)) {
            $this->warn('❌ Backup priečinok neexistuje!');
            return;
        }

        $files = glob($backupDir . '/erotikon_backup_*.sql*');
        
        if (empty($files)) {
            $this->warn('❌ Žiadne zálohy nenájdené!');
            return;
        }

        // Zoraď súbory podle času (najnovšie prvé)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        foreach ($files as $file) {
            $size = $this->formatBytes(filesize($file));
            $date = date('Y-m-d H:i:s', filemtime($file));
            $this->line("  - " . basename($file) . " ({$size}, {$date})");
        }
    }

    private function getLatestBackup($backupDir)
    {
        $files = glob($backupDir . '/erotikon_backup_*.sql*');
        
        if (empty($files)) {
            return null;
        }

        // Zoraď súbory podle času (najnovšie prvé)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return $files[0];
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
} 