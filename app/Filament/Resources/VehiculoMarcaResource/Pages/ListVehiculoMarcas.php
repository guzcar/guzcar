<?php

namespace App\Filament\Resources\VehiculoMarcaResource\Pages;

use App\Filament\Resources\VehiculoMarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehiculoMarcas extends ListRecords
{
    protected static string $resource = VehiculoMarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
