<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student','instructor','admin','staff','finance') NOT NULL DEFAULT 'student'");
        }

        // Seed: staff test account akpatel2029@gmail.com (password same as admin: 12345678)
        if (!DB::table('users')->where('email', 'akpatel2029@gmail.com')->exists()) {
            DB::table('users')->insert([
                'name'       => 'AK Patel',
                'email'      => 'akpatel2029@gmail.com',
                'password'   => Hash::make('12345678'),
                'role'       => 'staff',
                'is_blocked' => false,
                'position'   => 'Support Staff',
                'salary'     => 15000,
                'salary_currency' => 'INR',
                'salary_cycle'    => 'monthly',
                'joining_date'    => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed: finance test account finance@learningcurve.com (password: 12345678)
        if (!DB::table('users')->where('email', 'finance@learningcurve.com')->exists()) {
            DB::table('users')->insert([
                'name'       => 'Finance Dept',
                'email'      => 'finance@learningcurve.com',
                'password'   => Hash::make('12345678'),
                'role'       => 'finance',
                'is_blocked' => false,
                'position'   => 'Finance Department',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student','instructor','admin','staff') NOT NULL DEFAULT 'student'");
        }
        DB::table('users')->where('email', 'akpatel2029@gmail.com')->delete();
        DB::table('users')->where('email', 'finance@learningcurve.com')->delete();
    }
};
