<?php

namespace App\Filament\Resources\TrabajoOtroResource\Pages;

use App\Filament\Resources\TrabajoOtroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajoOtros extends ListRecords
{
    protected static string $resource = TrabajoOtroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
