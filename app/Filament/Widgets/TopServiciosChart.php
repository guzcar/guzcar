<?php

namespace App\Filament\Widgets;

use App\Models\TrabajoServicio;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopServiciosChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Servicios Más Ejecutados';
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '300px';

    protected $listeners = [
        'updateDateFilter' => 'updateFilteredData',
        'clearDateFilter' => 'clearFilters'
    ];

    public ?array $filters = [];

    protected function getData(): array
    {
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

        $data = TrabajoServicio::query()
            ->join('trabajos', 'trabajo_servicios.trabajo_id', '=', 'trabajos.id')
            ->join('servicios', 'trabajo_servicios.servicio_id', '=', 'servicios.id')
            ->whereNull('trabajos.deleted_at')
            ->whereDate('trabajos.fecha_ingreso', '>=', $startDate)
            ->whereDate('trabajos.fecha_ingreso', '<=', $endDate)
            ->select('servicios.nombre', DB::raw('count(*) as total'))
            ->groupBy('servicios.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Solicitudes',
                    'data' => $data->pluck('total'),
                    'backgroundColor' => [
                        '#3b82f6', '#ef4444', '#22c55e', '#eab308', '#a855f7'
                    ],
                    'borderWidth' => 0, // Estética: quita bordes blancos
                ],
            ],
            'labels' => $data->pluck('nombre'),
        ];
    }

    // ESTO ES LO QUE ELIMINA LOS EJES X e Y
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
                    'position' => 'bottom', // Mueve la leyenda abajo para que se vea mejor
                ],
            ],
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
