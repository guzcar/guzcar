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
            'name' => 'Administrador',
            'email' => 'admin@guzcar.com',
            'password' => Hash::make('123456789'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Miluska Ygnacio',
            'email' => 'miluska@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Nataly Guzman',
            'email' => 'nataly@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Elard Guzman',
            'email' => 'elard@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Moises',
            'email' => 'moises@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Jauregui',
            'email' => 'jauregui@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Yahir Alcantara',
            'email' => 'yahir@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Jose Peña',
            'email' => 'jose@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Daniel Rojas',
            'email' => 'daniel@guzcar.com',
            'password' => Hash::make('123456789'),
        ]);
    }
}
