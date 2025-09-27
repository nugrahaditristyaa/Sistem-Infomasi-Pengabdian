<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'name' => 'Admin Utama',
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['username' => 'inqa'],
            [
                'password' => Hash::make('inqa123'),
                'name' => 'Staff InQA',
                'role' => 'Staff InQA',
            ]
        );
    }
}
