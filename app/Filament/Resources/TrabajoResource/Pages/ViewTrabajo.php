<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use App\Models\Trabajo;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewTrabajo extends ViewRecord
{
    protected static string $resource = TrabajoResource::class;

    public function getViewData(): array
    {
        $trabajo = $this->record;

        $trabajo->load([
            // 'servicios',
            'detalles'
        ]);

        $evidencias = $trabajo->evidencias()
            ->orderBy('created_at', 'desc')
            ->get();

        // Observaciones únicas (sin nulos/blank, trim y case-insensitive)
        $observaciones = $trabajo->evidencias()
            ->whereNotNull('observacion')
            ->pluck('observacion')
            ->map(fn ($o) => trim($o))
            ->filter(fn ($o) => $o !== '')
            ->unique(fn ($o) => Str::lower($o))
            ->values();

        return [
            'trabajo'        => $trabajo,
            'evidencias'     => $evidencias,
            'observaciones'  => $observaciones, // <- pásalas a la vista
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
                ->color('gray'),
            // Action::make('Descargar')
            //     ->icon('heroicon-s-arrow-down-tray')
            //     ->url(
            //         fn(Trabajo $trabajo): string => route('trabajo.pdf.presupuesto', ['trabajo' => $trabajo]),
            //         shouldOpenInNewTab: true
            //     )
            //     ->color('gray'),
            EditAction::make(),
        ];
    }
}
