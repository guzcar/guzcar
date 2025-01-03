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
            'ubicacion' => 'Chimbote, ...',
        ]);

        Taller::create([
            'nombre' => 'Taller 2',
            'ubicacion' => 'Chimbote, ...',
        ]);
    }
}
