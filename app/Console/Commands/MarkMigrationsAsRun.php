<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkMigrationsAsRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mark-as-run {migration? : Názov migrácie na označenie (bez .php)} {--all : Označí všetky WordPress migrácie}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Označí migrácie ako spustené bez ich skutočného spustenia';

    /**
     * WordPress migrácie, ktoré sa majú označiť
     */
    protected $wordpressMigrations = [
        '2025_01_22_000000_add_wordpress_fields_to_erotic_clubs',
        '2025_01_22_000001_add_wordpress_fields_to_blog_posts',
        '2025_06_22_182603_add_wordpress_fields_to_ads_table'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migration = $this->argument('migration');
        $markAll = $this->option('all');

        if (!$migration && !$markAll) {
            $this->error('Musíte zadať názov migrácie alebo použiť --all pre všetky WordPress migrácie.');
            return 1;
        }

        $migrationsToMark = [];

        if ($markAll) {
            $migrationsToMark = $this->wordpressMigrations;
            $this->info('Označujem všetky WordPress migrácie ako spustené...');
        } else {
            $migrationsToMark = [$migration];
            $this->info("Označujem migráciu '{$migration}' ako spustenú...");
        }

        $batch = $this->getNextBatchNumber();
        $marked = 0;

        foreach ($migrationsToMark as $migrationName) {
            // Skontrolujeme či migrácia už nie je označená ako spustená
            $exists = DB::table('migrations')
                ->where('migration', $migrationName)
                ->exists();

            if ($exists) {
                $this->warn("Migrácia '{$migrationName}' je už označená ako spustená. Preskakujem.");
                continue;
            }

            // Označíme migráciu ako spustenú
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $batch
            ]);

            $this->info("✅ Migrácia '{$migrationName}' označená ako spustená.");
            $marked++;
        }

        if ($marked > 0) {
            $this->info("Úspešne označené {$marked} migrácie ako spustené v batch čísle {$batch}.");
        } else {
            $this->info("Žiadne migrácie neboli označené - všetky už boli spustené.");
        }

        return 0;
    }

    /**
     * Získa ďalšie batch číslo pre migrácie
     */
    protected function getNextBatchNumber(): int
    {
        $maxBatch = DB::table('migrations')->max('batch');
        return $maxBatch ? $maxBatch + 1 : 1;
    }
}
