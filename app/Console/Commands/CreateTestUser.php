<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vytvorí testovacieho používateľa pre testovanie impersonation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Skontrolovať či už existuje
        $existingUser = User::where('email', 'test@example.com')->first();
        
        if ($existingUser) {
            $this->info('Testovací používateľ už existuje:');
            $this->line('Meno: ' . $existingUser->name);
            $this->line('Email: ' . $existingUser->email);
            $this->line('Heslo: password123');
            return;
        }

        // Vytvoriť nového používateľa
        $user = User::create([
            'name' => 'Test Používateľ',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $this->info('✅ Testovací používateľ úspešne vytvorený!');
        $this->line('Meno: ' . $user->name);
        $this->line('Email: ' . $user->email);
        $this->line('Heslo: password123');
        $this->line('');
        $this->line('Môžete sa teraz prihlásiť ako admin a prepnúť sa na tohto používateľa.');
    }
}
