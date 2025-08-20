<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use App\Models\Trabajo;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewContabilidad extends ViewRecord
{
    protected static string $resource = ContabilidadResource::class;

    public function getViewData(): array
    {
        $trabajo = $this->record;

        // Observaciones únicas (sin nulos/blank, trim y case-insensitive)
        $observaciones = $trabajo->evidencias()
            ->whereNotNull('observacion')
            ->pluck('observacion')
            ->map(fn ($o) => trim($o))
            ->filter(fn ($o) => $o !== '')
            ->unique(fn ($o) => Str::lower($o))
            ->values();

        return [
            'trabajo' => $trabajo,
            'observaciones'  => $observaciones,
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
