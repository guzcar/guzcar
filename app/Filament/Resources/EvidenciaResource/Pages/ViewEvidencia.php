<?php

namespace App\Filament\Resources\EvidenciaResource\Pages;

use App\Filament\Resources\EvidenciaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewEvidencia extends ViewRecord
{
    protected static string $resource = EvidenciaResource::class;

    public function getViewData(): array
    {
        $evidencia = $this->record;

        return [
            'evidencia' => $evidencia,
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.evidencia.view';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Regresar')
                ->url(EvidenciaResource::getUrl())
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}
