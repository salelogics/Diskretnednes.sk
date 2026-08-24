<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rebrand: renames the super admin login email from admin@erotikon.sk to
     * admin@diskretnednes.sk on already-provisioned installs. Only updates the
     * row if it still has the old email (idempotent, safe to re-run) and only
     * if no account already uses the new address (avoids a unique constraint
     * clash). The password is left untouched.
     */
    public function up(): void
    {
        $newEmailTaken = DB::table('users')->where('email', 'admin@diskretnednes.sk')->exists();

        if (!$newEmailTaken) {
            DB::table('users')
                ->where('email', 'admin@erotikon.sk')
                ->update(['email' => 'admin@diskretnednes.sk']);
        }
    }

    public function down(): void
    {
        $oldEmailTaken = DB::table('users')->where('email', 'admin@erotikon.sk')->exists();

        if (!$oldEmailTaken) {
            DB::table('users')
                ->where('email', 'admin@diskretnednes.sk')
                ->update(['email' => 'admin@erotikon.sk']);
        }
    }
};
