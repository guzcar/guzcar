<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContabilidades extends ListRecords
{
    protected static string $resource = ContabilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
