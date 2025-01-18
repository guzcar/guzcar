<?php

namespace App\Filament\Resources\TrabajoPagoResource\Pages;

use App\Filament\Resources\TrabajoPagoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajoPago extends EditRecord
{
    protected static string $resource = TrabajoPagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
