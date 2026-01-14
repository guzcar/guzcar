<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TrabajoResource;
use App\Models\TrabajoPago;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PagosTableWidget extends BaseWidget
{
    // Evitamos el Lazy Loading para prevenir errores de inicialización en el dashboard
    protected static bool $isLazy = false;

    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Resumen de Pagos Recibidos';

    public ?string $startDate = null;
    public ?string $endDate = null;

    protected $listeners = [
        'updateDateFilter' => 'updateFilteredData',
        'clearDateFilter' => 'clearFilters'
    ];

    public function mount()
    {
        if (session()->has('dashboard_start_date')) {
            $this->startDate = session('dashboard_start_date');
            $this->endDate = session('dashboard_end_date');
        } else {
            $this->startDate = now()->startOfWeek()->format('Y-m-d');
            $this->endDate = now()->endOfDay()->format('Y-m-d');
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TrabajoPago::query()
                    ->with(['trabajo.vehiculo.clientes']) // Pre-cargamos para optimizar
                    ->when($this->startDate, fn ($query) => $query->whereDate('fecha_pago', '>=', $this->startDate))
                    ->when($this->endDate, fn ($query) => $query->whereDate('fecha_pago', '<=', $this->endDate))
            )
            ->defaultSort('fecha_pago', 'desc')
            ->columns([
                // GRUPO VEHÍCULO (Ahora contiene el link al trabajo)
                ColumnGroup::make('Vehículo', [
                    TextColumn::make('trabajo.vehiculo.placa')
                        ->label('Placa')
                        ->weight('bold')
                        ->searchable()
                        // Movimos la URL aquí ya que quitamos el código
                        ->url(function (TrabajoPago $record): ?string {
                            if ($record->trabajo && auth()->user()->can('update_trabajo')) {
                                return TrabajoResource::getUrl('edit', ['record' => $record->trabajo]);
                            } elseif ($record->trabajo && auth()->user()->can('view_trabajo')) {
                                return TrabajoResource::getUrl('view', ['record' => $record->trabajo]);
                            }
                            return null;
                        })
                        ->color('primary'),

                    TextColumn::make('trabajo.vehiculo.marca.nombre')
                        ->label('Marca')
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),

                    TextColumn::make('trabajo.vehiculo.modelo.nombre')
                        ->label('Modelo')
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),

                // GRUPO CLIENTES (Tu código adaptado)
                ColumnGroup::make('Cliente', [
                    TextColumn::make('clientes_display')
                        ->label('Clientes')
                        ->badge()
                        ->wrap()
                        ->color('info') // Color para el badge
                        ->placeholder('Sin Clientes')
                        // Búsqueda individual adaptada a la relación desde Pago -> Trabajo -> Vehiculo
                        ->searchable(
                            isIndividual: true,
                            query: function (Builder $query, string $search): Builder {
                                return $query->whereHas('trabajo.vehiculo.clientes', function ($q) use ($search) {
                                    $q->where('nombre', 'like', "%{$search}%");
                                });
                            }
                        )
                        // Lógica para mostrar Nombre (Teléfono)
                        ->getStateUsing(function ($record) {
                            // $record es TrabajoPago, accedemos a trabajo->vehiculo
                            $vehiculo = $record->trabajo?->vehiculo;

                            if (!$vehiculo || $vehiculo->clientes->isEmpty()) {
                                return null;
                            }

                            // Usamos la colección de clientes del vehículo
                            $clientes = $vehiculo->clientes->map(function ($cliente) {
                                if (!empty($cliente->telefono)) {
                                    $telefonoFormateado = $this->formatearTelefono($cliente->telefono);
                                    return "{$cliente->nombre} ({$telefonoFormateado})";
                                }
                                return $cliente->nombre;
                            });

                            return $clientes->toArray();
                        }),
                ]),

                // GRUPO PAGO
                ColumnGroup::make('Detalle Pago', [
                    TextColumn::make('fecha_pago')
                        ->label('Fecha')
                        ->date('d/m/Y')
                        ->sortable(),
                    
                    TextColumn::make('detalle.nombre') // Metodo de pago (Yape, Efectivo, etc)
                        ->label('Método')
                        ->badge()
                        ->color('gray'),

                    TextColumn::make('monto')
                        ->label('Monto')
                        ->prefix('S/ ')
                        ->weight('bold')
                        ->sortable()
                        ->summarize([
                            Sum::make()
                                ->label('Total')
                                ->money('PEN')
                        ]),
                ]),
            ])
            ->paginated([5, 10, 25]);
    }

    // Función auxiliar para formatear el teléfono (requerida por tu lógica)
    protected function formatearTelefono($telefono)
    {
        // Eliminar caracteres no numéricos
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        // Si tiene 9 dígitos (celular Perú), darle formato XXX XXX XXX
        if (strlen($telefono) == 9) {
            return substr($telefono, 0, 3) . ' ' . substr($telefono, 3, 3) . ' ' . substr($telefono, 6, 3);
        }

        return $telefono;
    }

    public function updateFilteredData(array $filters): void
    {
        $this->startDate = $filters['startDate'];
        $this->endDate = $filters['endDate'];
    }

    public function clearFilters(): void
    {
        $this->startDate = null;
        $this->endDate = null;
    }

    public static function canView(): bool
    {
        return auth()->user()->can('view_trabajo::pago');
    }
}