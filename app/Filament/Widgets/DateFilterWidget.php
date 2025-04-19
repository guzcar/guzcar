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
        $this->data = [
            'startDate' => Carbon::now()->startOfWeek()->toDateString(),
            'endDate' => Carbon::now()->toDateString()
        ];

        $this->readyToFilter = true;
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
            // Convertimos las fechas al formato Y-m-d que usa la base de datos
            $startDate = Carbon::parse($this->data['startDate'])->format('Y-m-d');
            $endDate = Carbon::parse($this->data['endDate'])->format('Y-m-d');

            $this->dispatch('updateDateFilter', [
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } else {
            $this->dispatch('clearDateFilter');
        }
    }
}
