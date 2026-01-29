<?php

namespace App\Filament\Resources\ControlEquipoResource\Pages;

use App\Filament\Resources\ControlEquipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListControlEquipos extends ListRecords
{
    protected static string $resource = ControlEquipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}