<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--force : Force create admin even if exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vytvorí admin používateľa pre DiskretneDnes.sk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'spravca@prosimsi.sk';
        $password = '24Atomofka11';
        $name = 'Správca';

        // Skontrolujeme či admin už existuje
        $existingAdmin = User::where('email', $email)->first();

        if ($existingAdmin && $existingAdmin->is_admin && !$this->option('force')) {
            $this->info("Admin používateľ s emailom {$email} už existuje a je pripravený na použitie!");
            $this->newLine();
            $this->info("=== PRIHLASOVACIE ÚDAJE ===");
            $this->info("Email: {$email}");
            $this->info("Heslo: {$password}");
            $this->info("==========================");
            $this->newLine();
            $this->info("Ak chcete prepísať existujúceho používateľa, použite: php artisan admin:create --force");
            return 0;
        }

        if ($existingAdmin && $this->option('force')) {
            $this->info("Aktualizujem existujúceho používateľa na admin...");
            $existingAdmin->update([
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
            $this->info("Používateľ bol úspešne aktualizovaný na admin!");
        } else if ($existingAdmin && !$existingAdmin->is_admin) {
            $this->info("Používateľ existuje ako bežný používateľ. Aktualizujem na admin...");
            $existingAdmin->update([
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
            $this->info("Bežný používateľ bol úspešne povýšený na admin!");
        } else {
            $this->info("Vytváram nového admin používateľa...");
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
            $this->info("Admin používateľ bol úspešne vytvorený!");
        }

        $this->newLine();
        $this->info("=== PRIHLASOVACIE ÚDAJE ===");
        $this->info("Email: {$email}");
        $this->info("Heslo: {$password}");
        $this->info("==========================");
        $this->newLine();

        return 0;
    }
}
