<?php

namespace App\Filament\Resources\TrabajoPagoDetalleResource\Pages;

use App\Filament\Resources\TrabajoPagoDetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajoPagoDetalle extends EditRecord
{
    protected static string $resource = TrabajoPagoDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
