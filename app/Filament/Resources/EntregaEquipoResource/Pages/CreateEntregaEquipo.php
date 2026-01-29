<?php

namespace App\Filament\Resources\EntregaEquipoResource\Pages;

use App\Filament\Resources\EntregaEquipoResource;
use App\Models\EquipoDetalle;
use Filament\Resources\Pages\CreateRecord;

class CreateEntregaEquipo extends CreateRecord
{
    protected static string $resource = EntregaEquipoResource::class;

    protected function afterCreate(): void
    {
        /** @var \App\Models\EntregaEquipo $entrega */
        $entrega = $this->record;

        // 1. Buscar los implementos actuales dentro del equipo seleccionado
        // Filtramos por deleted_at NULL (items activos)
        $itemsActuales = EquipoDetalle::where('equipo_id', $entrega->equipo_id)->get();

        // 2. Crear los detalles de la entrega (copia histórica)
        foreach ($itemsActuales as $item) {
            $entrega->detalles()->create([
                'implemento_id' => $item->implemento_id,
            ]);
        }
    }
}