<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Insert the default admin user.
     * Uses updateOrCreate logic so re-running never duplicates.
     */
    public function up(): void
    {
        $email = 'admin@gmail.com';

        $exists = DB::table('users')->where('email', $email)->first();

        if ($exists) {
            // Always keep role=admin and correct password, never remove access
            DB::table('users')->where('email', $email)->update([
                'name'       => 'Admin',
                'role'       => 'admin',
                'password'   => Hash::make('admin'),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('users')->insert([
                'name'       => 'Admin',
                'email'      => $email,
                'password'   => Hash::make('admin'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Do NOT delete admin on rollback — intentional
    }
};
