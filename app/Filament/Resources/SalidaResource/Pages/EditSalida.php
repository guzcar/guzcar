<?php

namespace App\Filament\Resources\SalidaResource\Pages;

use App\Filament\Resources\SalidaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalida extends EditRecord
{
    protected static string $resource = SalidaResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        $data['responsable'] = $record->responsable->name;
        $data['stock'] = $record->articulo->stock;
        $data['abiertos'] = $record->articulo->abiertos;

        $articulo = $record->articulo;

        if ($articulo) {

            $data['fraccionable'] = $articulo->fraccionable;
            $data['abiertos'] = $articulo->fraccionable ? $articulo->abiertos : null;

            $cantidad = $record->cantidad;

            if ($articulo->fraccionable) {
                if (in_array($cantidad, [0.25, 0.50, 0.75, 1])) {
                    $data['cantidad_fraccion'] = $cantidad;
                    $data['cantidad_custom'] = null;
                } else {
                    $data['cantidad_fraccion'] = 'custom';
                    $data['cantidad_custom'] = $cantidad;
                }
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
