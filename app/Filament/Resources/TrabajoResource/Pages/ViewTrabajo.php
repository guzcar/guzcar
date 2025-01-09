<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrabajo extends ViewRecord
{
    protected static string $resource = TrabajoResource::class;

    public function getViewData(): array
    {
        $trabajo = $this->record;

        return [
            'trabajo' => $trabajo,
            'evidencias' => $trabajo->evidencias, // Obtén todas las evidencias asociadas
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.trabajo.view';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Regresar')
                ->url(TrabajoResource::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
