<?php

namespace App\Filament\Resources\ImplementoIncidenciaResource\Pages;

use App\Filament\Resources\ImplementoIncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImplementoIncidencias extends ListRecords
{
    protected static string $resource = ImplementoIncidenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}