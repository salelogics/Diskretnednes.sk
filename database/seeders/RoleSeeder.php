<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Administrátor',
            'slug' => 'admin',
        ]);

        Role::create([
            'name' => 'Amatérka',
            'slug' => 'amateur',
        ]);

        Role::create([
            'name' => 'Klub',
            'slug' => 'club',
        ]);
    }
} 