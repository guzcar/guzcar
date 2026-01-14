<?php

namespace App\Filament\Widgets;

use App\Models\Trabajo;
use App\Models\TrabajoPago;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CountStatuses extends BaseWidget
{
    protected static ?int $sort = 1;

    protected $listeners = [
        'updateDateFilter' => 'updateFilteredData',
        'clearDateFilter' => 'clearFilters'
    ];

    public ?array $filters = [];
    public bool $hasFilters = false;

    protected function getStats(): array
    {
        // 1. Recuperar fechas (Prioridad: Filtro > Sesión > Defecto)
        if ($this->hasFilters) {
            $startDate = Carbon::parse($this->filters['startDate']);
            $endDate = Carbon::parse($this->filters['endDate']);
        } elseif (session()->has('dashboard_start_date')) {
            $startDate = Carbon::parse(session('dashboard_start_date'));
            $endDate = Carbon::parse(session('dashboard_end_date'));
        } else {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfDay();
        }

        // 2. Consultas para los estados de Trabajos (filtro por fecha de ingreso)
        $queryCobrados = Trabajo::where('desembolso', 'COBRADO')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate);

        $queryACuenta = Trabajo::where('desembolso', 'A CUENTA')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate);

        $queryPorCobrar = Trabajo::where('desembolso', 'POR COBRAR')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate);

        // 3. Consulta para INGRESOS (filtro por fecha de pago real)
        $totalIngresos = TrabajoPago::whereDate('fecha_pago', '>=', $startDate)
            ->whereDate('fecha_pago', '<=', $endDate)
            ->sum('monto');

        // 4. Retornar los 4 Stats
        return [
            Stat::make('Cobrados', $queryCobrados->count())
                ->label('Trabajos cobrados')
                ->icon('heroicon-m-check-circle')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),

            Stat::make('A cuentas', $queryACuenta->count())
                ->label('Trabajos a cuenta')
                ->icon('heroicon-m-clock')
                ->chart([3, 5, 3, 6, 5, 3, 7, 3])
                ->color('warning'),

            Stat::make('Por cobrar', $queryPorCobrar->count())
                ->label('Trabajos por cobrar')
                ->icon('heroicon-m-x-circle')
                ->chart([2, 5, 8, 4, 2, 7, 1, 6])
                ->color('danger'),
            
            Stat::make('Ingresos', 'S/ ' . number_format($totalIngresos, 2))
                ->label('Total Ingresos')
                ->icon('heroicon-m-currency-dollar')
                ->chart([4, 6, 3, 7, 4, 8, 5, 9])
                ->color('primary'), // Color solicitado
        ];
    }

    protected function getColumns(): int
    {
        return 4; // Forzar 4 columnas en una sola fila
    }

    public function updateFilteredData(array $filters): void
    {
        $this->filters = $filters;
        $this->hasFilters = true;
        $this->dispatch('updateStats');
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->hasFilters = false;
        $this->dispatch('updateStats');
    }
    
    public static function canView(): bool
    {
        return auth()->user()->can('view_trabajo::pago');
    }
}