<?php

namespace App\Filament\Resources\TrabajoInformePlantillaResource\Pages;

use App\Filament\Resources\TrabajoInformePlantillaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTrabajoInformePlantillas extends ManageRecords
{
    protected static string $resource = TrabajoInformePlantillaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear plantilla')
                ->modalWidth('screen'),
        ];
    }
}
