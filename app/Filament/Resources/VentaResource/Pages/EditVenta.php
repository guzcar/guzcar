<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\ClienteVehiculo;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVenta extends EditRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

        $relacionExistente = ClienteVehiculo::where('cliente_id', $clienteId)
            ->where('vehiculo_id', $vehiculoId)
            ->exists();

        if (!$relacionExistente) {
            ClienteVehiculo::create([
                'cliente_id' => $clienteId,
                'vehiculo_id' => $vehiculoId,
            ]);
        }

        return $data;
    }
}
