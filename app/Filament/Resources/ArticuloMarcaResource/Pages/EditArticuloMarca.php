<?php

namespace App\Filament\Resources\ArticuloMarcaResource\Pages;

use App\Filament\Resources\ArticuloMarcaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticuloMarca extends EditRecord
{
    protected static string $resource = ArticuloMarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
