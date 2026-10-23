<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\BcaSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user — safe to run multiple times
        User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'name'     => 'Admin',
            'password' => Hash::make('admin'),
            'role'     => 'admin',
        ]);

        $this->call(BcaSeeder::class);
        $this->call(QuestionSeeder::class);
    }
}
