<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use App\Models\Trabajo;
use App\Services\TrabajoService;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTrabajo extends EditRecord
{
    protected static string $resource = TrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),

            Action::make('historialVehiculo')
                ->label('Historial')
                ->icon('heroicon-o-clock')
                ->color('success')
                ->modalHeading('Historial de Mantenimientos, Filtros y Aceites')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalWidth('4xl')
                ->modalContent(function (Trabajo $record) {
                    $trabajosAnteriores = Trabajo::with([
                        'trabajoArticulos.articulo.categoria',
                        'trabajoArticulos.articulo.marca',
                        'trabajoArticulos.articulo.subCategoria',
                        'trabajoArticulos.articulo.presentacion',
                        'trabajoArticulos.articulo.unidad',
                        'otros'
                    ])
                        ->where('vehiculo_id', $record->vehiculo_id)
                        ->where('id', '!=', $record->id)
                        ->orderBy('fecha_ingreso', 'desc')
                        ->get();

                    $historialAgrupado = [];
                    $palabrasClave = ['filtro', 'aceite', 'arandela'];

                    foreach ($trabajosAnteriores as $trabajo) {
                        $fechaTrabajo = $trabajo->fecha_ingreso ? $trabajo->fecha_ingreso->format('d/m/Y') : 'Sin fecha';

                        // Evaluamos si el string de descripción contiene 'mantenimiento'
                        $esMantenimiento = !empty($trabajo->descripcion_servicio) && str_contains(strtolower($trabajo->descripcion_servicio), 'mantenimiento');

                        $elementosDelTrabajo = [];

                        // Evaluamos Artículos
                        foreach ($trabajo->trabajoArticulos as $ta) {
                            $articulo = $ta->articulo;
                            if (!$articulo || !$articulo->categoria)
                                continue;

                            $categoriaNombre = strtolower($articulo->categoria->nombre);
                            $pasaFiltro = false;

                            foreach ($palabrasClave as $palabra) {
                                if (str_contains($categoriaNombre, $palabra)) {
                                    $pasaFiltro = true;
                                    break;
                                }
                            }

                            if ($pasaFiltro) {
                                $parts = array_filter([
                                    $articulo->categoria->nombre ?? null,
                                    $articulo->marca->nombre ?? null,
                                    $articulo->subCategoria->nombre ?? null,
                                    $articulo->especificacion ?? null,
                                    $articulo->presentacion->nombre ?? null,
                                    $articulo->medida ?? null,
                                    $articulo->unidad->nombre ?? null,
                                    $articulo->color ?? null,
                                ]);
                                $nombreCompleto = implode(' ', $parts);

                                $elementosDelTrabajo[] = [
                                    'tipo' => 'articulo',
                                    'nombre' => $nombreCompleto ?: 'Artículo sin nombre',
                                    'cantidad' => $ta->cantidad,
                                    'stock' => $articulo->stock,
                                ];
                            }
                        }

                        // Evaluamos Otros
                        foreach ($trabajo->otros as $otro) {
                            if (!$otro->descripcion)
                                continue;

                            $descripcion = strtolower($otro->descripcion);
                            $pasaFiltro = false;

                            foreach ($palabrasClave as $palabra) {
                                if (str_contains($descripcion, $palabra)) {
                                    $pasaFiltro = true;
                                    break;
                                }
                            }

                            if ($pasaFiltro) {
                                $elementosDelTrabajo[] = [
                                    'tipo' => 'otro',
                                    'nombre' => $otro->descripcion,
                                    'cantidad' => $otro->cantidad,
                                    'stock' => null,
                                ];
                            }
                        }

                        $tieneElementos = count($elementosDelTrabajo) > 0;

                        // Si el trabajo tuvo "mantenimiento" O si arrojó resultados de artículos/otros
                        if ($esMantenimiento || $tieneElementos) {
                            if (!isset($historialAgrupado[$fechaTrabajo])) {
                                $historialAgrupado[$fechaTrabajo] = [
                                    'info_trabajos' => [],
                                    'elementos' => []
                                ];
                            }

                            // Guardamos SIEMPRE la descripción y kilometraje del trabajo relevante
                            $historialAgrupado[$fechaTrabajo]['info_trabajos'][] = [
                                'descripcion' => $trabajo->descripcion_servicio ?? 'Sin descripción registrada.',
                                'kilometraje' => $trabajo->kilometraje,
                            ];

                            // Guardamos los elementos (si los hay)
                            foreach ($elementosDelTrabajo as $el) {
                                $historialAgrupado[$fechaTrabajo]['elementos'][] = $el;
                            }
                        }
                    }

                    return view('filament.resources.trabajo.modals.historial-vehiculo', [
                        'historialAgrupado' => $historialAgrupado,
                    ]);
                }),

            // TODO: Buscar una mejor terminologia
            Action::make('Ver Inventario')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->label('Inventario')
                ->action(function () {
                    // 1. Guarda los datos del formulario (esto no cambia).
                    $this->save();

                    // 2. ✅ Redirige a tu ruta nombrada de Laravel.
                    return redirect()->route('admin.trabajos.inventario', [
                        'trabajo' => $this->getRecord()
                    ]);
                }),

            ActionGroup::make([
                Action::make('Descargar Check List')
                    ->icon('heroicon-s-clipboard-document-check')
                    ->url(
                        fn(Trabajo $trabajo): string => route('pdf.admin.inventario.ingreso', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),

                Action::make('Descargar informe')
                    ->icon('heroicon-s-document-text')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.informe', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    )
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::informe')),

                Action::make('Descargar evidencias')
                    ->icon('heroicon-s-photo')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.evidencia', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    )
                    ->hidden(fn() => !auth()->user()->can('view_evidencia')),
            ])
                ->button()
                ->label('Descargar')
                ->icon('heroicon-s-arrow-down-tray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Configura la acción del botón de cancelar para que redirija al index.
     */
    protected function getCancelFormAction(): Actions\Action
    {
        return Actions\Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->url($this->getResource()::getUrl('index'))
            ->color('gray');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        TrabajoService::actualizarTrabajoPorId($record);
        return $record;
    }
}
