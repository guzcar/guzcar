<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\ClienteVehiculo;
use App\Models\Venta;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

class EditVenta extends EditRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Descargar')
                ->icon('heroicon-s-arrow-down-tray')
                ->form([
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
                ])
                ->action(function (Venta $venta, array $data, $livewire) {

                    $params = [
                        'igv' => $data['igv'] ?? false,
                        'igv_porcentaje' => $data['igv_porcentaje'] ?? 18,
                    ];

                    $url = route('ventas.pdf', ['venta' => $venta] + $params);
                    $livewire->js("window.open('{$url}', '_blank');");
                })
                ->modalHeading('Configuración de Descarga')
                ->modalButton('Descargar')
                ->modalWidth('md'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['responsable'] = $this->record->responsable->name;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $clienteId = $data['cliente_id'];
        $vehiculoId = $data['vehiculo_id'];

        if ($vehiculoId) {

            $relacionExistente = ClienteVehiculo::where('cliente_id', $clienteId)
                ->where('vehiculo_id', $vehiculoId)
                ->exists();

            if (!$relacionExistente) {
                ClienteVehiculo::create([
                    'cliente_id' => $clienteId,
                    'vehiculo_id' => $vehiculoId,
                ]);
            }
        }

        return $data;
    }
}
