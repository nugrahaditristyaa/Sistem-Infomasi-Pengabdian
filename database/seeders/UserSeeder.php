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

        User::firstOrCreate(
            ['username' => 'kaprodi_ti'],
            [
                'password' => Hash::make('kaprodi123'),
                'name' => 'Kaprodi Teknik Informatika',
                'role' => 'Kaprodi TI',
            ]
        );

        User::firstOrCreate(
            ['username' => 'kaprodi_si'],
            [
                'password' => Hash::make('kaprodi123'),
                'name' => 'Kaprodi Sistem Informasi',
                'role' => 'Kaprodi SI',
            ]
        );
    }
}
