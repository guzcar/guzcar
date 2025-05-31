<?php

namespace App\Filament\Resources\TrabajoDetalleResource\Pages;

use App\Filament\Resources\TrabajoDetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajoDetalle extends EditRecord
{
    protected static string $resource = TrabajoDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
