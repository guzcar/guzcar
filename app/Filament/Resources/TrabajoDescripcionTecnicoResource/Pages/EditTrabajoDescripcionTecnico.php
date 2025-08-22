<?php

namespace App\Filament\Resources\TrabajoDescripcionTecnicoResource\Pages;

use App\Filament\Resources\TrabajoDescripcionTecnicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajoDescripcionTecnico extends EditRecord
{
    protected static string $resource = TrabajoDescripcionTecnicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
