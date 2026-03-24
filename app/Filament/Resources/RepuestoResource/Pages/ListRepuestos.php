<?php

namespace App\Filament\Resources\RepuestoResource\Pages;

use App\Filament\Resources\RepuestoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepuestos extends ListRecords
{
    protected static string $resource = RepuestoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
