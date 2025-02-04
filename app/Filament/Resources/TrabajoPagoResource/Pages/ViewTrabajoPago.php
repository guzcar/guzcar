<?php

namespace App\Filament\Resources\TrabajoPagoResource\Pages;

use App\Filament\Resources\TrabajoPagoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTrabajoPago extends ViewRecord
{
    protected static string $resource = TrabajoPagoResource::class;

    public function getViewData(): array
    {
        $pago = $this->record;

        return [
            'pago' => $pago,
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.trabajo_pago.view';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Regresar')
                ->url(TrabajoPagoResource::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}
