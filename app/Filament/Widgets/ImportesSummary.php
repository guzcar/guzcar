<?php

namespace App\Filament\Widgets;

use App\Models\Trabajo;
use App\Models\TrabajoPago;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ImportesSummary extends BaseWidget
{
    protected static ?int $sort = 2;

    protected $listeners = [
        'updateDateFilter' => 'updateFilteredData',
        'clearDateFilter' => 'clearFilters'
    ];

    public ?array $filters = [];
    public bool $hasFilters = false;

    protected function getStats(): array
    {
        // Fechas por defecto (semana actual)
        $startDate = $this->hasFilters
            ? Carbon::parse($this->filters['startDate'])
            : Carbon::now()->startOfWeek(); // Lunes de esta semana

        $endDate = $this->hasFilters
            ? Carbon::parse($this->filters['endDate'])
            : Carbon::now()->endOfDay(); // Hoy

        // Consulta para EGRESOS (trabajos por fecha_ingreso)
        $totalEgresos = Trabajo::whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate)
            ->sum('importe');

        // Consulta para INGRESOS (pagos por fecha_pago)
        $totalIngresos = TrabajoPago::whereDate('fecha_pago', '>=', $startDate)
            ->whereDate('fecha_pago', '<=', $endDate)
            ->sum('monto');

        return [
            Stat::make('Ingresos por Trabajos', 'S/ ' . number_format($totalIngresos, 2))
                ->icon('heroicon-m-credit-card')
                ->chart([1, 1])
                ->color('success'),

            Stat::make('Egresos por Trabajos', 'S/ ' . number_format($totalEgresos, 2))
                ->icon('heroicon-m-banknotes')
                ->chart([1, 1])
                ->color('danger'),
        ];
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

    protected function getColumns(): int
    {
        return 2;
    }
    
    public static function canView(): bool
    {
        return auth()->user()->can('view_trabajo::pago');
    }
}
