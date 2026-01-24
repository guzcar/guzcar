<?php

namespace App\Filament\Resources\EntregaMaletaResource\Pages;

use App\Filament\Resources\EntregaMaletaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntregaMaletas extends ListRecords
{
    protected static string $resource = EntregaMaletaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
