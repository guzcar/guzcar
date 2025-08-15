<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use App\Models\Contabilidad;
use App\Models\Trabajo;
use App\Services\TrabajoService;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContabilidad extends EditRecord
{
    protected static string $resource = ContabilidadResource::class;

    protected array $comprobanteIds = [];

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
                    ->action(function (Contabilidad $trabajo, array $data, $livewire) {

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
                        fn(Contabilidad $trabajo): string => route('trabajo.pdf.proforma', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),
            ])
                ->button()
                ->label('Descargar')
                ->icon('heroicon-s-arrow-down-tray'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->comprobanteIds = collect($data['comprobantes'] ?? [])
            ->pluck('comprobante_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        unset($data['comprobantes']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->comprobantes()->sync($this->comprobanteIds);
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
