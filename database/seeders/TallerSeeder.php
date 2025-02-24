<?php

namespace Database\Seeders;

use App\Models\Taller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TallerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Taller::create([
            'nombre' => 'Taller 1',
            'ubicacion' => 'Prolongacion Leoncio Prado 1540, Chimbote 02801',
        ]);

        Taller::create([
            'nombre' => 'Taller 2',
            'ubicacion' => '1550 Pje. 2 de Mayo Chimbote, Áncash',
        ]);
    }
}
