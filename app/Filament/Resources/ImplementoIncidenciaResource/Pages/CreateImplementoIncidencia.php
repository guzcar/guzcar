<?php

namespace App\Filament\Resources\ImplementoIncidenciaResource\Pages;

use App\Filament\Resources\ImplementoIncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\EquipoDetalle;

class CreateImplementoIncidencia extends CreateRecord
{
    protected static string $resource = ImplementoIncidenciaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Responsable = usuario actual
        $data['responsable_id'] = auth()->id();

        // Ajustar campos según el tipo de origen
        if ($data['tipo_origen'] === 'EQUIPO') {
            // Para EQUIPO: cantidad siempre es 1
            $data['cantidad'] = 1;
            // implemento_id se debe obtener del equipo_detalle
            if (!empty($data['equipo_detalle_id'])) {
                $equipoDetalle = EquipoDetalle::find($data['equipo_detalle_id']);
                $data['implemento_id'] = $equipoDetalle?->implemento_id;
            }
            // propietario_id ya viene del form (hidden field)
        } else {
            // Para STOCK: limpiar campos de equipo
            $data['equipo_detalle_id'] = null;
            $data['propietario_id'] = null;
            // cantidad y implemento_id ya vienen correctos del form
        }

        // Limpiar campos temporales del UI
        unset($data['equipo_id']);
        unset($data['responsable_nombre']);
        unset($data['propietario_nombre']);
        unset($data['max_cantidad']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}