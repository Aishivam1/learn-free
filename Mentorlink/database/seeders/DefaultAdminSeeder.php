<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'g@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('ganeshanamh'),
                'role' => 'admin',
                'points' => 0,
                'badges' => null
            ]
        );
    }
}