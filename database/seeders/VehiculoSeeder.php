<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vehiculo::create([
            'placa' => 'ANX-802',
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'color' => 'Azul',
            'tipo_vehiculo_id' => 3,
        ]);

        Vehiculo::create([
            'placa' => 'D8H-539',
            'marca' => 'Kia',
            'modelo' => 'Rio',
            'color' => 'Plomo',
            'tipo_vehiculo_id' => 1,
        ]);

        Vehiculo::create([
            'placa' => 'BRL-831',
            'marca' => 'Foton',
            'modelo' => 'Auman',
            'color' => 'Azul/Blanco',
            'tipo_vehiculo_id' => 2,
        ]);

        $faker = Faker::create();  // Inicializamos Faker

        // Crear 1000 vehículos con Faker
        for ($i = 0; $i < 1000; $i++) {
            Vehiculo::create([
                'placa' => strtoupper($faker->regexify('[A-Z]{3}-[0-9]{3}')),  // Genera una placa aleatoria (ej. "ABC-123")
                'marca' => $faker->company,  // Genera una marca aleatoria (ej. "Toyota")
                'modelo' => $faker->word,  // Genera un modelo aleatorio (ej. "Hilux")
                'color' => $faker->safeColorName,  // Genera un color aleatorio
                'tipo_vehiculo_id' => $faker->numberBetween(1, 5),  // Asume que tienes 5 tipos de vehículos (puedes ajustar según tu caso)
            ]);
        }
    }
}
