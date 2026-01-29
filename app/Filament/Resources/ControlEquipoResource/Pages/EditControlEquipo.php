<?php

namespace App\Filament\Resources\ControlEquipoResource\Pages;

use App\Filament\Resources\ControlEquipoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditControlEquipo extends EditRecord
{
    protected static string $resource = ControlEquipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('asignacionPdf')
                    ->label('Hoja de asignación')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => route('pdf.control_equipo.asignacion', $record)) // Ruta actualizada
                    ->openUrlInNewTab(),
            // Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['responsable_nombre'] = $this->record->responsable?->name;
        $data['propietario_nombre'] = $this->record->propietario?->name;
        $data['equipo_codigo'] = $this->record->equipo?->codigo; // Corregido maleta_codigo

        return $data;
    }
}