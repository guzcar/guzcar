<?php

namespace App\Filament\Resources\TrabajoDetalleResource\Pages;

use App\Filament\Resources\TrabajoDetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajoDetalles extends ListRecords
{
    protected static string $resource = TrabajoDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
