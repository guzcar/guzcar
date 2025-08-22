<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use App\Models\Trabajo;
use App\Models\TrabajoDescripcionTecnico;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ViewTrabajo extends ViewRecord
{
    protected static string $resource = TrabajoResource::class;

    public function getViewData(): array
    {
        $trabajo = $this->record;

        $trabajo->load([
            'detalles',
            'articulos.categoria',
            'articulos.marca',
            'articulos.subCategoria.categoria',
            'articulos.presentacion',
            'articulos.unidad',
        ]);

        // Descripciones de técnicos (con usuario, y tolerante a nulos):
        $trabajoDescripcionTecnicos = TrabajoDescripcionTecnico::with('user')
            ->where('trabajo_id', $trabajo->id ?? null)
            ->latest()
            ->get();

        // Artículos con movimiento de salida, agrupados y sumados:
        // Artículos con movimiento de salida, agrupados y sumados:
        $articulosSalidosResumen = collect($trabajo->articulos ?? [])
            ->groupBy('id')
            ->map(function (Collection $items) {
                $a = $items->first();

                // Suma de cantidades desde el pivot
                $cantidad = $items->sum(function ($it) {
                    $raw = $it->pivot->cantidad ?? 0;
                    return is_numeric($raw) ? (float) $raw : 0.0;
                });

                // Nombre completo EXACTO (solo si existen), separados por espacio:
                // 1) Categoría  2) Marca  3) Subcategoría  4) Especificación
                // 5) Presentación  6) Medida  7) Unidad  8) Color
                $categoriaNombre = optional($a->categoria)->nombre
                    ?? optional(optional($a->subCategoria)->categoria)->nombre; // por si viene por subCategoría
    
                $parts = array_values(array_filter([
                    $categoriaNombre,
                    optional($a->marca)->nombre,
                    optional($a->subCategoria)->nombre,
                    $a->especificacion,
                    optional($a->presentacion)->nombre,
                    $a->medida,
                    optional($a->unidad)->nombre,
                    $a->color,
                ], fn($v) => is_string($v) && trim($v) !== ''));

                $nombre = trim(implode(' ', $parts));
                if ($nombre === '' && isset($a->nombre) && trim($a->nombre) !== '') {
                    // Fallback amable si no hay partes
                    $nombre = trim($a->nombre);
                }
                if ($nombre === '') {
                    $nombre = 'Artículo';
                }

                return [
                    'id' => $a->id ?? null,
                    'nombre' => $nombre,
                    'cantidad' => $cantidad,
                ];
            })
            ->values();


        $evidencias = $trabajo->evidencias()
            ->orderBy('created_at', 'desc')
            ->get();

        // Observaciones únicas (sin nulos/blank, trim y case-insensitive)
        $observaciones = $trabajo->evidencias()
            ->whereNotNull('observacion')
            ->pluck('observacion')
            ->map(fn($o) => trim($o))
            ->filter(fn($o) => $o !== '')
            ->unique(fn($o) => Str::lower($o))
            ->values();

        return [
            'trabajo' => $trabajo,
            'evidencias' => $evidencias,
            'observaciones' => $observaciones,
            'trabajoDescripcionTecnicos' => $trabajoDescripcionTecnicos,
            'articulosSalidosResumen' => $articulosSalidosResumen,
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
