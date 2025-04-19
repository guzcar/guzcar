<?php

namespace App\Filament\Widgets;

use App\Models\Trabajo;
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
        // Establecemos fechas por defecto (semana actual)
        $startDate = $this->hasFilters
            ? Carbon::parse($this->filters['startDate'])
            : Carbon::now()->startOfWeek(); // Lunes de esta semana

        $endDate = $this->hasFilters
            ? Carbon::parse($this->filters['endDate'])
            : Carbon::now()->endOfDay(); // Hoy

        // Consultas base con filtro de fechas
        $queryCobrados = Trabajo::where('desembolso', 'COBRADO')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate);

        $queryACuenta = Trabajo::where('desembolso', 'A CUENTA')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate);

        $queryPorCobrar = Trabajo::where('desembolso', 'POR COBRAR')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate);

        return [
            Stat::make('Cobrados', $queryCobrados->count())
                ->label('Trabajos cobrados')
                ->icon('heroicon-m-check-circle')
                ->chart([1, 1])
                ->color('success'),

            Stat::make('A cuentas', $queryACuenta->count())
                ->label('Trabajos a cuenta')
                ->icon('heroicon-m-clock')
                ->chart([1, 1])
                ->color('warning'),

            Stat::make('Por cobrar', $queryPorCobrar->count())
                ->label('Trabajos por cobrar')
                ->icon('heroicon-m-x-circle')
                ->chart([1, 1])
                ->color('danger'),
        ];
    }

    protected function formatDate($date): string
    {
        return Carbon::parse($date)->format('d/m/Y');
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
        return 3;
    }
    
    public static function canView(): bool
    {
        return auth()->user()->can('view_trabajo::pago');
    }
}
