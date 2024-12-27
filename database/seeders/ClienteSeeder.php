<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $faker = Faker::create();

        // Crear 10 clientes de ejemplo con DNI o RUC
        foreach (range(1, 10) as $index) {
            // Alternar entre generar un DNI o un RUC para las empresas
            if ($index % 2 == 0) {
                // Crear un cliente con DNI (persona natural)
                $identificador = $this->generarDni();
                $nombre = $faker->name; // Nombre de persona
            } else {
                // Crear un cliente con RUC (empresa)
                $identificador = $this->generarRuc();
                $nombre = $faker->company; // Nombre de empresa
            }

            // Crear el cliente en la base de datos
            Cliente::create([
                'identificador' => $identificador, // DNI o RUC
                'nombre' => $nombre, // Nombre de persona o empresa
            ]);
        }
    }

    /**
     * Generar un DNI peruano aleatorio (8 dígitos).
     *
     * @return string
     */
    private function generarDni()
    {
        // Genera un DNI aleatorio de 8 dígitos (solo números)
        return str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Generar un RUC peruano aleatorio (11 dígitos).
     *
     * @return string
     */
    private function generarRuc()
    {
        // RUC de empresa, comienza con '10' y luego 9 dígitos aleatorios
        return '10' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
    }
}
