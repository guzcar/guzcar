<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Servicio::create([
            'nombre' => 'Mantenimiento de motor',
            'costo' => 250,
        ]);

        Servicio::create([
            'nombre' => 'Cambio de aceite y filtro',
            'costo' => 120,
        ]);

        Servicio::create([
            'nombre' => 'Revisión y cambio de filtros (aire, combustible, cabin filter)',
            'costo' => 100,
        ]);

        Servicio::create([
            'nombre' => 'Revisión y relleno de niveles de líquidos',
            'costo' => 60,
        ]);

        Servicio::create([
            'nombre' => 'Revisión y cambio de correas y mangueras',
            'costo' => 150,
        ]);

        Servicio::create([
            'nombre' => 'Lavado y siliconeado de motor',
            'costo' => 80,
        ]);

        Servicio::create([
            'nombre' => 'Revisión y reparación de sistema de frenos',
            'costo' => 200,
        ]);

        Servicio::create([
            'nombre' => 'Revisión de suspensión',
            'costo' => 300,
        ]);

        Servicio::create([
            'nombre' => 'Revisión de sistema de climatización',
            'costo' => 120,
        ]);

        Servicio::create([
            'nombre' => 'Alineación y balanceo de llantas',
            'costo' => 100,
        ]);
    }
}
