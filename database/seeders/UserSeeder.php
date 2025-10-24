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
            ['username' => 'staf_fti'],
            [
                'password' => Hash::make('staf123'),
                'name' => 'Staf FTI',
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['username' => 'dekan'],
            [
                'password' => Hash::make('dekan123'),
                'name' => 'Dekan',
                'role' => 'Dekan',
            ]
        );

        User::firstOrCreate(
            ['username' => 'kaprodi_ti'],
            [
                'password' => Hash::make('kaproditi123'),
                'name' => 'Kaprodi Informatika',
                'role' => 'Kaprodi TI',
            ]
        );

        User::firstOrCreate(
            ['username' => 'kaprodi_si'],
            [
                'password' => Hash::make('kaprodisi123'),
                'name' => 'Kaprodi Sistem Informasi',
                'role' => 'Kaprodi SI',
            ]
        );
    }
}
