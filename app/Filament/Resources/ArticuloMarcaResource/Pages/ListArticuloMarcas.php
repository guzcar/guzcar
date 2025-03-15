<?php

namespace App\Filament\Resources\ArticuloMarcaResource\Pages;

use App\Filament\Resources\ArticuloMarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticuloMarcas extends ListRecords
{
    protected static string $resource = ArticuloMarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
