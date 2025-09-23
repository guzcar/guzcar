<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use App\Models\Contabilidad;
use App\Models\Trabajo;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewContabilidad extends ViewRecord
{
    protected static string $resource = ContabilidadResource::class;

    public function getViewData(): array
    {
        $trabajo = $this->record;

        // CARGAR RELACIONES NECESARIAS PARA LOS CÁLCULOS
        $trabajo->load([
            'servicios',
            'trabajoArticulos.articulo',
            'otros',
            'descuentos' // Asegúrate de cargar los descuentos
        ]);

        // Observaciones únicas (sin nulos/blank, trim y case-insensitive)
        $observaciones = $trabajo->evidencias()
            ->whereNotNull('observacion')
            ->pluck('observacion')
            ->map(fn($o) => trim($o))
            ->filter(fn($o) => $o !== '')
            ->unique(fn($o) => Str::lower($o))
            ->values();

        // --- NUEVO: CÁLCULOS FINANCIEROS ---
        // 1. Calcular subtotales (igual que en el controlador de presupuesto)
        $subtotal_servicios = $trabajo->servicios->sum(function ($trabajoServicio) {
            return $trabajoServicio->cantidad * $trabajoServicio->precio;
        });

        $subtotal_articulos = $trabajo->trabajoArticulos->sum(function ($trabajoArticulo) {
            return $trabajoArticulo->cantidad * $trabajoArticulo->precio;
        });

        $subtotal_trabajo_otros = $trabajo->otros->sum(function ($trabajoOtro) {
            return $trabajoOtro->cantidad * $trabajoOtro->precio;
        });

        // 2. Total base (antes de descuentos e IGV)
        $total_base = $subtotal_articulos + $subtotal_servicios + $subtotal_trabajo_otros;

        // 3. Calcular montos de descuentos individuales y total
        $descuentos_calculados = [];
        $total_descuentos = 0;

        if ($trabajo->descuentos->isNotEmpty()) {
            foreach ($trabajo->descuentos as $descuento) {
                $monto_descuento = $total_base * ($descuento->descuento / 100);
                $descuentos_calculados[] = [
                    'descuento' => $descuento, // El modelo completo por si necesitas más datos
                    'monto' => $monto_descuento
                ];
                $total_descuentos += $monto_descuento;
            }
        }

        // 4. Total después de descuentos
        $total_con_descuentos = $total_base - $total_descuentos;

        return [
            'trabajo' => $trabajo,
            'observaciones' => $observaciones,
            // Pasar los cálculos nuevos a la vista
            'total_base' => $total_base,
            'descuentos_calculados' => $descuentos_calculados,
            'total_descuentos' => $total_descuentos,
            'total_con_descuentos' => $total_con_descuentos,
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

            ActionGroup::make([

                Action::make('Descargar presupuesto')
                    ->icon('heroicon-s-document-currency-dollar')
                    ->url(
                        fn(Contabilidad $trabajo): string => route('trabajo.pdf.presupuesto', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),

                ActionGroup::make([
                    Action::make('Presupuesto Servicios')
                        ->icon('heroicon-o-document-currency-dollar')
                        ->url(
                            fn(Contabilidad $trabajo): string => route('trabajo.pdf.presupuesto-servicios', ['trabajo' => $trabajo]),
                            shouldOpenInNewTab: true
                        ),
                    Action::make('Presupuesto Repuestos')
                        ->icon('heroicon-o-document-currency-dollar')
                        ->url(
                            fn(Contabilidad $trabajo): string => route('trabajo.pdf.presupuesto-articulos-repuestos-otros', ['trabajo' => $trabajo]),
                            shouldOpenInNewTab: true
                        ),
                ])
                    ->dropdown(false),

                Action::make('Descargar proforma')
                    ->icon('heroicon-s-document')
                    ->url(
                        fn(Contabilidad $trabajo): string => route('trabajo.pdf.proforma', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),
            ])
                ->color('gray')
                ->button()
                ->label('Descargar')
                ->icon('heroicon-s-arrow-down-tray'),
            EditAction::make(),
        ];
    }
}
