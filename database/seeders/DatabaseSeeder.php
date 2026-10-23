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
        // Admin
        User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'name'     => 'Admin',
            'password' => Hash::make('admin'),
            'role'     => 'admin',
        ]);

        // Sample courses seeding removed to prevent fake data.
        $this->call(BcaSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(BcaSeeder::class);
    }
}
