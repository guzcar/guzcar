<?php

namespace App\Filament\Resources\EntregaEquipoResource\Pages;

use App\Filament\Resources\EntregaEquipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaEquipos extends ListRecords
{
    protected static string $resource = EntregaEquipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}