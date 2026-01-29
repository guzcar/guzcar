<?php

namespace App\Filament\Resources\ImplementoEntradaResource\Pages;

use App\Filament\Resources\ImplementoEntradaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImplementoEntradas extends ListRecords
{
    protected static string $resource = ImplementoEntradaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}