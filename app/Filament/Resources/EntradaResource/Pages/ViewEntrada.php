<?php

namespace App\Filament\Resources\EntradaResource\Pages;

use App\Filament\Resources\EntradaResource;
use App\Models\Trabajo;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEntrada extends ViewRecord
{
    protected static string $resource = EntradaResource::class;

    public function getViewData(): array
    {
        $entrada = $this->record;

        return [
            'entrada' => $entrada,
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.entrada.view';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Regresar')
                ->url(EntradaResource::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
            // Action::make('Descargar')
            //     ->icon('heroicon-s-arrow-down-tray')
            //     ->url(
            //         fn(Trabajo $trabajo): string => route('trabajo.pdf.presupuesto', ['trabajo' => $trabajo]),
            //         shouldOpenInNewTab: true
            //     )
            //     ->color('gray'),
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
