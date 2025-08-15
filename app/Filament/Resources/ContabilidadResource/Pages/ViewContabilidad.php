<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use App\Models\Trabajo;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContabilidad extends ViewRecord
{
    protected static string $resource = ContabilidadResource::class;

    public function getViewData(): array
    {
        $trabajo = $this->record;

        return [
            'trabajo' => $trabajo,
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.contabilidad.view';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Regresar')
                ->url(ContabilidadResource::getUrl())
                ->color('gray'),
            EditAction::make(),
        ];
    }
}
