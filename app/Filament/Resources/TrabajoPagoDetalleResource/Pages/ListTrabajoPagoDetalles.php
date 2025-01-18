<?php

namespace App\Filament\Resources\TrabajoPagoDetalleResource\Pages;

use App\Filament\Resources\TrabajoPagoDetalleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajoPagoDetalles extends ListRecords
{
    protected static string $resource = TrabajoPagoDetalleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
