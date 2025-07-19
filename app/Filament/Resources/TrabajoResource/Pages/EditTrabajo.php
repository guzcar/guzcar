<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use App\Models\Trabajo;
use App\Services\TrabajoService;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTrabajo extends EditRecord
{
    protected static string $resource = TrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),
            ActionGroup::make([

                Action::make('Descargar presupuesto')
                    ->icon('heroicon-s-document-currency-dollar')
                    ->form([
                        // Grid::make()
                        //     ->schema([
                        //         Section::make()
                        //             ->schema([
                        Checkbox::make('igv')
                            ->label('Incluir IGV')
                            ->reactive(),
                        TextInput::make('igv_porcentaje')
                            ->label('Porcentaje')
                            ->suffix('%')
                            ->default('18')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->disabled(function (callable $get) {
                                return !$get('igv');
                            }),
                        // ])
                        // ->heading('Configuración de IGV')
                        // ->columnSpan(['xl' => 1, 'lg' => 1, 'md' => 1, 'sm' => 1]),

                        // Section::make()
                        //     ->schema([
                        //         Checkbox::make('servicios')
                        //             ->label('Incluir servicios')
                        //             ->default(true),
                        //         Checkbox::make('articulos')
                        //             ->label('Incluir artículos')
                        //             ->default(true),
                        //     ])
                        //     ->heading('Opciones de Descarga')
                        //     ->columnSpan(['xl' => 1, 'lg' => 1, 'md' => 1, 'sm' => 1]),
                        // ])
                        // ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                    ])
                    ->action(function (Trabajo $trabajo, array $data, $livewire) {

                        $params = [
                            'igv' => $data['igv'] ?? false,
                            'igv_porcentaje' => $data['igv_porcentaje'] ?? 18,
                            // 'servicios' => $data['servicios'] ?? true,
                            // 'articulos' => $data['articulos'] ?? true,
                        ];

                        $url = route('trabajo.pdf.presupuesto', ['trabajo' => $trabajo] + $params);
                        $livewire->js("window.open('{$url}', '_blank');");
                    })
                    ->modalHeading('Configuración de Descarga')
                    ->modalButton('Descargar')
                    ->modalWidth('md')
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago')),

                Action::make('Descargar proforma')
                    ->icon('heroicon-s-document')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.proforma', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),

                Action::make('Descargar informe')
                    ->icon('heroicon-s-document-text')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.informe', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    )
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::informe')),

                Action::make('Descargar evidencias')
                    ->icon('heroicon-s-photo')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.evidencia', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    )
                    ->hidden(fn() => !auth()->user()->can('view_evidencia')),
            ])
                ->button()
                ->label('Descargar')
                ->icon('heroicon-s-arrow-down-tray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        TrabajoService::actualizarTrabajoPorId($record);
        return $record;
    }
}
