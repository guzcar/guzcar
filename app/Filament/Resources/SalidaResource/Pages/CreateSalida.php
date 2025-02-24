<?php

namespace App\Filament\Resources\SalidaResource\Pages;

use App\Filament\Resources\SalidaResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateSalida extends CreateRecord
{
    protected static string $resource = SalidaResource::class;

    protected ?string $heading = 'Registrar Salida';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['responsable_id'] = auth()->user()->id;
        return $data;
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Registrar')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->label('Registrar y registrar otro')
            ->action('createAnother')
            ->keyBindings(['mod+shift+s'])
            ->color('gray');
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
