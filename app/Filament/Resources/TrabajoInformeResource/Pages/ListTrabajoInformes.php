<?php

namespace App\Filament\Resources\TrabajoInformeResource\Pages;

use App\Filament\Resources\TrabajoInformeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajoInformes extends ListRecords
{
    protected static string $resource = TrabajoInformeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
