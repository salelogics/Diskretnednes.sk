<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--compress} {--keep=10}';
    protected $description = 'Vytvorí zálohu databázy';

    public function handle()
    {
        $this->info('🗄️ Začínam zálohovanie databázy...');

        $config = config('database.connections.' . config('database.default'));
        
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // Vytvor backup priečinok
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Vytvor timestamp
        $timestamp = date('Ymd_His');
        $filename = "diskretnednes_backup_{$timestamp}.sql";
        $filepath = $backupDir . '/' . $filename;

        $this->info("📁 Vytváram zálohu do: {$filename}");

        // Vytvor mysqldump command
        $command = sprintf(
            'mysqldump -h%s -P%s -u%s -p%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        // Spusti command
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info('✅ Záloha úspešne vytvorená!');

            // Kompresuj ak je požadované
            if ($this->option('compress')) {
                $this->info('🗜️ Kompresovanie zálohy...');
                exec("gzip {$filepath}");
                $filepath .= '.gz';
                $filename .= '.gz';
            }

            // Zobraz veľkosť súboru
            $size = $this->formatBytes(filesize($filepath));
            $this->info("📊 Veľkosť súboru: {$size}");

            // Vyčisti staré zálohy
            $keep = (int) $this->option('keep');
            $this->cleanOldBackups($backupDir, $keep);

            $this->info('🎉 Zálohovanie dokončené!');
            return 0;
        } else {
            $this->error('❌ Chyba pri vytváraní zálohy!');
            return 1;
        }
    }

    private function cleanOldBackups($backupDir, $keep)
    {
        $this->info("🧹 Čistím staré zálohy (ponechávam {$keep} najnovších)...");
        
        // Zachytí staré aj nové pomenovanie záloh, aby sa rotovali spoločne
        $files = array_merge(
            glob($backupDir . '/diskretnednes_backup_*.sql*'),
            glob($backupDir . '/erotikon_backup_*.sql*')
        );

        // Zoraď súbory podle času vytvorenia (najnovšie prvé)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Zmaž súbory nad limit
        $deleted = 0;
        for ($i = $keep; $i < count($files); $i++) {
            if (unlink($files[$i])) {
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("🗑️ Zmazané {$deleted} starých záloh");
        }

        // Zobraz aktuálne zálohy
        $remaining = array_slice($files, 0, $keep);
        $this->info("📋 Aktuálne zálohy:");
        foreach ($remaining as $file) {
            $size = $this->formatBytes(filesize($file));
            $date = date('Y-m-d H:i:s', filemtime($file));
            $this->line("  - " . basename($file) . " ({$size}, {$date})");
        }
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