<?php

namespace App\Filament\Widgets;

use App\Models\Trabajo;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TopClientesChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Clientes Frecuentes';
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '300px';

    protected $listeners = [
        'updateDateFilter' => 'updateFilteredData',
        'clearDateFilter' => 'clearFilters'
    ];

    public ?array $filters = [];

    protected function getData(): array
    {
        // 1. Obtener rango de fechas
        if (!empty($this->filters['startDate'])) {
            $startDate = Carbon::parse($this->filters['startDate']);
            $endDate = Carbon::parse($this->filters['endDate']);
        } elseif (session()->has('dashboard_start_date')) {
            $startDate = Carbon::parse(session('dashboard_start_date'));
            $endDate = Carbon::parse(session('dashboard_end_date'));
        } else {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfDay();
        }

        if ($startDate->gt($endDate)) {
            $endDate = $startDate->copy()->endOfDay();
        }

        // 2. Obtener trabajos
        $trabajos = Trabajo::query()
            ->with(['cliente', 'vehiculo.clientes']) 
            ->whereNull('deleted_at')
            ->whereDate('fecha_ingreso', '>=', $startDate)
            ->whereDate('fecha_ingreso', '<=', $endDate)
            ->get();

        // 3. Procesar: Mapear -> Filtrar Nulos -> Contar
        $conteoClientes = $trabajos->map(function ($trabajo) {
            $cliente = $trabajo->firstCliente();
            // Retornamos el nombre o null si no existe
            return $cliente ? ($cliente->nombre ?? $cliente->razon_social ?? 'Cliente #' . $cliente->id) : null;
        })
        ->filter() // <--- ESTO ELIMINA LOS NULL (DESCONOCIDOS)
        ->countBy()
        ->sortDesc()
        ->take(5);

        return [
            'datasets' => [
                [
                    'label' => 'Trabajos realizados',
                    'data' => $conteoClientes->values()->all(),
                    'backgroundColor' => [
                        '#6366f1', '#ec4899', '#14b8a6', '#f59e0b', '#8b5cf6'
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $conteoClientes->keys()->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function updateFilteredData(array $filters): void
    {
        $this->filters = $filters;
        $this->dispatch('updateChartData');
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->dispatch('updateChartData');
    }
}