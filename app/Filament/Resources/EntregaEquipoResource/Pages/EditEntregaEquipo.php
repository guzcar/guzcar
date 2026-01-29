<?php

namespace App\Filament\Resources\EntregaEquipoResource\Pages;

use App\Filament\Resources\EntregaEquipoResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditEntregaEquipo extends EditRecord
{
    protected static string $resource = EntregaEquipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pdf')
                ->label('Acta de entrega')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn($record) => route('pdf.entrega_equipo.acta', $record)) // Ruta actualizada
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['responsable_nombre'] = $this->record->responsable?->name;
        $data['propietario_nombre'] = $this->record->propietario?->name ?? 'Sin Asignar';
        
        return $data;
    }
}