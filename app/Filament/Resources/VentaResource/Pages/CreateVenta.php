<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\ClienteVehiculo;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $clienteId = $data['cliente_id'];
        $vehiculoId = $data['vehiculo_id'];
        $data['responsable_id'] = auth()->user()->id;

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

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('edit', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
    }
}
