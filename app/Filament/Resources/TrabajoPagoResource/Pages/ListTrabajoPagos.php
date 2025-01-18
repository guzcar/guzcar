<?php

namespace App\Filament\Resources\TrabajoPagoResource\Pages;

use App\Filament\Resources\TrabajoPagoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajoPagos extends ListRecords
{
    protected static string $resource = TrabajoPagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
