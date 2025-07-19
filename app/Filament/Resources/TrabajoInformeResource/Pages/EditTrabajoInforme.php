<?php

namespace App\Filament\Resources\TrabajoInformeResource\Pages;

use App\Filament\Resources\TrabajoInformeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajoInforme extends EditRecord
{
    protected static string $resource = TrabajoInformeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
