<?php

namespace App\Filament\Resources\EntradaArticuloResource\Pages;

use App\Filament\Resources\EntradaArticuloResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntradaArticulos extends ListRecords
{
    protected static string $resource = EntradaArticuloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
