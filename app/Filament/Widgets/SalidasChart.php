<?php

namespace App\Filament\Widgets;

use App\Models\Trabajo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class SalidasChart extends ChartWidget
{
    protected static ?string $heading = 'Entrega de Vehículos';
    protected static ?int $sort = 4;

    protected $listeners = [
        'updateDateFilter' => 'updateFilteredData',
        'clearDateFilter' => 'clearFilters'
    ];

    public ?array $filters = [];
    public bool $hasFilters = false;

    protected function getData(): array
    {
        $meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

        if ($this->hasFilters) {
            $startDate = Carbon::parse($this->filters['startDate']);
            $endDate = Carbon::parse($this->filters['endDate']);
        } elseif (session()->has('dashboard_start_date')) {
            $startDate = Carbon::parse(session('dashboard_start_date'));
            $endDate = Carbon::parse(session('dashboard_end_date'));
        } else {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfDay();
        }

        // Determinar la granularidad del gráfico
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays <= 21) { // Hasta 5 semanas (mostrar por día)
            $period = CarbonPeriod::create($startDate, $endDate);
            $format = 'd';
            $groupBy = 'day';
            $xAxisFormat = 'd';
        } elseif ($diffInDays <= 45) { // Hasta 6 meses (mostrar por semana)
            $period = CarbonPeriod::create($startDate, '1 week', $endDate);
            $format = 'W';
            $groupBy = 'week';
            $xAxisFormat = 'W';
        } elseif ($diffInDays <= 550) { // Hasta 2 años (mostrar por mes)
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);
            $groupBy = 'month';
            $xAxisFormat = 'M';
        } else { // Más de 2 años (mostrar por año)
            $period = CarbonPeriod::create($startDate, '1 year', $endDate);
            $groupBy = 'year';
            $xAxisFormat = 'Y';
        }

        $labels = [];
        $data = [];

        foreach ($period as $date) {
            // Formatear según el tipo de agrupación
            if ($groupBy === 'week') {
                $labels[] = "Sem {$date->weekOfYear}";
            } elseif ($groupBy === 'month') {
                $mesNumero = $date->format('n') - 1;
                $labels[] = $meses[$mesNumero] . ' ' . $date->format('y');
            } elseif ($groupBy === 'year') {
                $labels[] = $date->format('Y');
            } else {
                $labels[] = $date->format($format) . ' ' . $meses[$date->format('n') - 1];
            }

            $query = Trabajo::whereNotNull('fecha_salida')
                ->whereDate('fecha_salida', '>=', $groupBy === 'week' ? $date->startOfWeek() : $date->startOf($groupBy))
                ->whereDate('fecha_salida', '<=', $groupBy === 'week' ? $date->endOfWeek() : $date->endOf($groupBy));

            $data[] = $query->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Vehículos entregados',
                    'data' => $data,
                    'backgroundColor' => '#22c55e', // Color verde de Filament (success)
                    'borderColor' => '#22c55e',
                    'fill' => false,
                    'tension' => 0.1,
                ],
            ],
            'options' => [
                'scales' => [
                    'x' => [
                        'ticks' => [
                            'maxRotation' => 45,
                            'minRotation' => 45,
                            'autoSkip' => true,
                            'maxTicksLimit' => 12
                        ]
                    ]
                ],
                'plugins' => [
                    'tooltip' => [
                        'callbacks' => [
                            'title' => function ($context) use ($groupBy, $meses) {
                                $label = $context[0]->label;
                                if ($groupBy === 'week') {
                                    return "Semana {$label}";
                                } elseif ($groupBy === 'month') {
                                    $parts = explode(' ', $label);
                                    return "{$parts[0]} {$parts[1]}";
                                }
                                return $label;
                            }
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        return 'Cantidad de vehículos que fueron entregados a sus propietarios.';
    }

    public function updateFilteredData(array $filters): void
    {
        $this->filters = $filters;
        $this->hasFilters = true;
        $this->dispatch('updateChartData');
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->hasFilters = false;
        $this->dispatch('updateChartData');
    }
}
