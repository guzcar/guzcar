<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use App\Models\Trabajo;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTrabajo extends CreateRecord
{
    protected static string $resource = TrabajoResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
            return $resource::getUrl('edit', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
        }

        if ($resource::hasPage('view') && $resource::canView($this->getRecord())) {
            return $resource::getUrl('view', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
        }

        return $resource::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Verificar si ya existe un trabajo activo para este vehículo
        $trabajoExistente = Trabajo::where('vehiculo_id', $data['vehiculo_id'])
            ->whereNull('fecha_salida')
            ->exists();

        if ($trabajoExistente) {
            Notification::make()
                ->title('Error')
                ->body('Este vehículo ya tiene un trabajo registrado en curso.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return Actions\Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->url($this->getResource()::getUrl('index'))
            ->color('gray');
    }
}
