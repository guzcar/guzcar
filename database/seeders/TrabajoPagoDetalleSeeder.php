<?php

namespace Database\Seeders;

use App\Models\TrabajoPagoDetalle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrabajoPagoDetalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TrabajoPagoDetalle::create(['nombre' => 'EFECT. MILUSKA']);
        TrabajoPagoDetalle::create(['nombre' => 'EFECT. ELARD']);
        TrabajoPagoDetalle::create(['nombre' => 'EFECT. ELARD GP.']);
        TrabajoPagoDetalle::create(['nombre' => 'TRANSF. ELARD GP.']);
        TrabajoPagoDetalle::create(['nombre' => 'TRANSF. BBVA GUZ.']);
        TrabajoPagoDetalle::create(['nombre' => 'TRANSF. BCP GUZ.']);
        TrabajoPagoDetalle::create(['nombre' => 'CULQUI']);
        TrabajoPagoDetalle::create(['nombre' => 'SIN COBRO']);
    }
}
