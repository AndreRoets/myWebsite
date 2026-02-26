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
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'Test@gmail.com'],
            [
                'name'        => 'Test',
                'surname'     => 'Admin',
                'password'    => Hash::make('Test@1024'),
                'is_admin'    => true,
                'is_approved' => true,
            ]
        );
    }
}