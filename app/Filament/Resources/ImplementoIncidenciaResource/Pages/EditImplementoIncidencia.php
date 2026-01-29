<?php

namespace App\Filament\Resources\ImplementoIncidenciaResource\Pages;

use App\Filament\Resources\ImplementoIncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImplementoIncidencia extends EditRecord
{
    protected static string $resource = ImplementoIncidenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        // Campos comunes
        $data['responsable_nombre'] = $record->responsable?->name;
        $data['tipo_origen_display'] = $record->tipo_origen === 'EQUIPO' ? 'Desde Equipo' : 'Desde Stock/Almacén';
        $data['motivo_display'] = $record->motivo;

        if ($record->tipo_origen === 'EQUIPO') {
            // Datos específicos de EQUIPO
            $data['propietario_nombre'] = $record->propietario?->name ?? 'No asignado';

            // Obtener equipo y implemento desde equipo_detalle
            $ed = $record->equipoDetalle()->withTrashed()->with(['equipo', 'implemento'])->first();

            $data['equipo_codigo'] = $ed?->equipo?->codigo ?? "Equipo #{$ed?->equipo_id}";
            $data['implemento_nombre'] = $ed?->implemento?->nombre ?? "Detalle #{$record->equipo_detalle_id}";

        } else {
            // Datos específicos de STOCK
            $data['implemento_nombre'] = $record->implemento?->nombre ?? "Implemento #{$record->implemento_id}";
            $data['cantidad_display'] = $record->cantidad;
            // Para STOCK, no mostrar información de equipo
            $data['equipo_codigo'] = null;
            $data['propietario_nombre'] = 'N/A';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Restricción de edición para proteger la integridad de los Triggers
        $allowedFields = ['motivo', 'observacion'];

        // Filtrar solo campos permitidos
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        // Mantener los demás campos del registro original
        foreach ($this->record->getAttributes() as $key => $value) {
            if (!in_array($key, $allowedFields)) {
                $filteredData[$key] = $value;
            }
        }

        return $filteredData;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}