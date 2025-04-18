<?php

namespace App\Filament\Resources\SalidaResource\Pages;

use App\Filament\Resources\SalidaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalida extends ListRecords
{
    protected static string $resource = SalidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Salida'),
        ];
    }
}
