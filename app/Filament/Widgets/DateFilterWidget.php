<?php

namespace App\Filament\Widgets;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class DateFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.date-filter-widget';
    protected static ?int $sort = -2;

    public ?array $data = [];
    public bool $readyToFilter = false;

    public function mount()
    {
        // CAMBIO IMPORTANTE: 
        // Ya NO leemos de la sesión (session('dashboard_start_date')).
        // Siempre forzamos el inicio a la semana actual.
        
        $defaultStart = Carbon::now()->startOfWeek()->toDateString();
        $defaultEnd = Carbon::now()->toDateString();

        $this->data = [
            'startDate' => $defaultStart,
            'endDate' => $defaultEnd
        ];

        // Sobrescribimos la sesión inmediatamente con la fecha actual.
        // Esto "borra" cualquier fecha antigua que haya quedado guardada
        // y asegura que los gráficos carguen con los datos de HOY.
        session([
            'dashboard_start_date' => $defaultStart,
            'dashboard_end_date' => $defaultEnd
        ]);

        $this->readyToFilter = true;
        
        // Enviamos el evento para que todos los gráficos se enteren
        $this->dispatchFilterUpdate();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('')
                    ->placeholder('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->live()
                    ->maxDate(now())
                    ->afterStateUpdated(function ($state) {
                        $this->readyToFilter = !empty($this->data['startDate']) && !empty($this->data['endDate']);
                        $this->dispatchFilterUpdate();
                    })
                    ->required(),

                DatePicker::make('endDate')
                    ->label('')
                    ->placeholder('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->live()
                    ->maxDate(now())
                    ->afterStateUpdated(function ($state) {
                        $this->readyToFilter = !empty($this->data['startDate']) && !empty($this->data['endDate']);
                        $this->dispatchFilterUpdate();
                    })
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function dispatchFilterUpdate(): void
    {
        if ($this->readyToFilter) {
            $startDate = Carbon::parse($this->data['startDate'])->format('Y-m-d');
            $endDate = Carbon::parse($this->data['endDate'])->format('Y-m-d');

            // Seguimos guardando en sesión cuando el usuario CAMBIA la fecha manualmente
            // para que la paginación de la tabla o recargas mantengan el filtro.
            session([
                'dashboard_start_date' => $startDate,
                'dashboard_end_date' => $endDate
            ]);

            $this->dispatch('updateDateFilter', [
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } else {
            $this->dispatch('clearDateFilter');
        }
    }
}
