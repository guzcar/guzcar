<?php

namespace App\Filament\Resources\VehiculoMarcaResource\Pages;

use App\Filament\Resources\VehiculoMarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehiculoMarca extends EditRecord
{
    protected static string $resource = VehiculoMarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
