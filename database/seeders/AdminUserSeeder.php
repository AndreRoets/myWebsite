<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'Test@gmail.com'],
            [
                'name' => 'Test',
                'password' => Hash::make('Test@12345'),
                'is_admin' => true,
            ]
        );
    }
}