<?php

namespace App\Filament\Resources\CategoriaRepuestoResource\Pages;

use App\Filament\Resources\CategoriaRepuestoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategoriaRepuestos extends ManageRecords
{
    protected static string $resource = CategoriaRepuestoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
