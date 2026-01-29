<?php

namespace App\Filament\Resources\ControlEquipoDetalleResource\Pages;

use App\Filament\Resources\ControlEquipoDetalleResource;
use Filament\Resources\Pages\ListRecords;

class ListControlEquipoDetalles extends ListRecords
{
    protected static string $resource = ControlEquipoDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}