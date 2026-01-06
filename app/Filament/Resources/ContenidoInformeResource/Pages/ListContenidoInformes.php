<?php

namespace App\Filament\Resources\ContenidoInformeResource\Pages;

use App\Filament\Resources\ContenidoInformeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContenidoInformes extends ListRecords
{
    protected static string $resource = ContenidoInformeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
