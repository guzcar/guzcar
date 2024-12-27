<?php

namespace Database\Seeders;

use App\Models\TipoVehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoVehiculo::create(['nombre' => 'Auto']);
        TipoVehiculo::create(['nombre' => 'Camion']);
        TipoVehiculo::create(['nombre' => 'Camioneta']);
        TipoVehiculo::create(['nombre' => 'Montacarga']);
        TipoVehiculo::create(['nombre' => 'Combi']);
        TipoVehiculo::create(['nombre' => 'Bus']);
        TipoVehiculo::create(['nombre' => 'Minivan']);
        TipoVehiculo::create(['nombre' => 'Camion Cisterna']);
        TipoVehiculo::create(['nombre' => 'E. de Baldes']);
        TipoVehiculo::create(['nombre' => 'Bomba']);
    }
}
