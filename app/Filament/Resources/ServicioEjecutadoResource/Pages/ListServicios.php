<?php

namespace App\Filament\Resources\ServicioEjecutadoResource\Pages;

use App\Filament\Resources\ServicioEjecutadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicios extends ListRecords
{
    protected static string $resource = ServicioEjecutadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
