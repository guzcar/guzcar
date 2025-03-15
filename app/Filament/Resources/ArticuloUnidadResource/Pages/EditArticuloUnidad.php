<?php

namespace App\Filament\Resources\ArticuloUnidadResource\Pages;

use App\Filament\Resources\ArticuloUnidadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticuloUnidad extends EditRecord
{
    protected static string $resource = ArticuloUnidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
