<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@guzcar.com.pe',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Miluska',
            'email' => 'miluska@guzcar.com.pe',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Nataly',
            'email' => 'nataly@guzcar.com.pe',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Elard',
            'email' => 'elard@guzcar.com.pe',
            'password' => Hash::make('123456789'),
        ]);
    }
}
