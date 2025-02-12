<?php

namespace App\Filament\Resources\ServicioEjecutadoResource\Pages;

use App\Filament\Resources\ServicioEjecutadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServicio extends EditRecord
{
    protected static string $resource = ServicioEjecutadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
