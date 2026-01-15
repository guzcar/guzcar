<?php

namespace App\Filament\Widgets;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker; // Importamos el campo del paquete

class DateFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.date-filter-widget';
    protected static ?int $sort = -2;

    public ?array $data = [];

    public function mount()
    {
        // Configuramos el rango inicial (Semana actual)
        $defaultStart = Carbon::now()->startOfWeek()->format('d/m/Y');
        $defaultEnd = Carbon::now()->format('d/m/Y');
        
        // El formato del plugin suele ser "d/m/Y - d/m/Y"
        $defaultRange = "{$defaultStart} - {$defaultEnd}";

        $this->form->fill([
            'dateRange' => $defaultRange,
        ]);

        // Guardamos en sesión inicial
        session([
            'dashboard_start_date' => Carbon::createFromFormat('d/m/Y', $defaultStart)->format('Y-m-d'),
            'dashboard_end_date' => Carbon::createFromFormat('d/m/Y', $defaultEnd)->format('Y-m-d'),
        ]);
        
        // No disparamos el evento en mount para evitar doble carga, 
        // ya que los gráficos leerán la sesión o el default por su cuenta.
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make('dateRange')
                    ->label('Filtrar por rango de fechas')
                    ->placeholder('Seleccione un rango')
                    // Importante: live() para que detecte el cambio, 
                    // pero este componente suele esperar a que cierres el calendario para disparar.
                    ->live()
                    // Configuraciones opcionales para UX
                    ->alwaysShowCalendar(false)
                    ->displayFormat('DD/MM/YYYY')
                    ->format('DD/MM/YYYY') // Formato interno del string
                    ->separator(' - ')
                    ->afterStateUpdated(function ($state) {
                        $this->dispatchFilterUpdate($state);
                    }),
            ])
            ->statePath('data');
    }

    protected function dispatchFilterUpdate(?string $state): void
    {
        // El plugin devuelve un string tipo "01/01/2024 - 31/01/2024"
        // Si el usuario limpia el campo, state será null
        
        if (empty($state)) {
            $this->dispatch('clearDateFilter');
            session()->forget(['dashboard_start_date', 'dashboard_end_date']);
            return;
        }

        // Separamos el string
        $dates = explode(' - ', $state);

        if (count($dates) === 2) {
            try {
                // Convertimos de d/m/Y a Y-m-d para la base de datos
                $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->format('Y-m-d');

                // Guardar en sesión
                session([
                    'dashboard_start_date' => $startDate,
                    'dashboard_end_date' => $endDate
                ]);

                // Emitir evento UNICO
                $this->dispatch('updateDateFilter', [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);

            } catch (\Exception $e) {
                // Si falla el parseo, no hacemos nada o limpiamos
            }
        }
    }
}
