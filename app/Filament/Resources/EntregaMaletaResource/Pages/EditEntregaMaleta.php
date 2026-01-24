<?php

namespace App\Filament\Resources\EntregaMaletaResource\Pages;

use App\Filament\Resources\EntregaMaletaResource;
use Filament\Resources\Pages\EditRecord;

class EditEntregaMaleta extends EditRecord
{
    protected static string $resource = EntregaMaletaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('pdf')
                ->label('Acta de entrega')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn($record) => route('pdf.entrega.acta', $record))
                ->openUrlInNewTab(),
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Rellenar los campos visuales que no están en el $fillable directo o son relaciones
        $data['responsable_nombre'] = $this->record->responsable?->name;
        $data['propietario_nombre'] = $this->record->propietario?->name ?? 'Sin Asignar';
        
        return $data;
    }
}