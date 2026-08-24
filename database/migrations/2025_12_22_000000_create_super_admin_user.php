<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vytvoríme super admin používateľa ak už neexistuje
        $adminExists = DB::table('users')->where('email', 'admin@diskretnednes.sk')->exists();
        
        if (!$adminExists) {
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'admin@diskretnednes.sk',
                'password' => Hash::make('SuperAdmin2024!'),
                'is_admin' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "Super admin používateľ bol úspešne vytvorený!\n";
            echo "Email: admin@diskretnednes.sk\n";
            echo "Heslo: SuperAdmin2024!\n";
        } else {
            echo "Super admin používateľ už existuje.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pri rollback môžeme odstrániť super admin používateľa
        DB::table('users')->where('email', 'admin@diskretnednes.sk')->delete();
    }
}; 