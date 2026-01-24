<?php

namespace App\Filament\Resources\EntregaMaletaResource\Pages;

use App\Filament\Resources\EntregaMaletaResource;
use App\Models\MaletaDetalle;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEntregaMaleta extends CreateRecord
{
    protected static string $resource = EntregaMaletaResource::class;

    /**
     * Lógica Mágica:
     * Una vez creada la cabecera de la entrega, copiamos el inventario actual
     * de la maleta a la tabla de detalles de la entrega.
     */
    protected function afterCreate(): void
    {
        /** @var \App\Models\EntregaMaleta $entrega */
        $entrega = $this->record;

        // 1. Buscar las herramientas actuales dentro de la maleta seleccionada
        $itemsActuales = MaletaDetalle::where('maleta_id', $entrega->maleta_id)->get();

        // 2. Crear los detalles de la entrega
        foreach ($itemsActuales as $item) {
            $entrega->detalles()->create([
                'herramienta_id' => $item->herramienta_id,
                // Si tuvieras 'estado' en el detalle de entrega, lo copiarías aquí:
                // 'estado_al_entregar' => $item->ultimo_estado
            ]);
        }
    }
}