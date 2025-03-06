<?php

namespace App\Filament\Resources\DespachoResource\Pages;

use App\Filament\Resources\DespachoResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDespacho extends CreateRecord
{
    protected static string $resource = DespachoResource::class;

    protected ?string $heading = 'Registrar Despacho';

    protected static bool $canCreateAnother = false;

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

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('edit', ['record' => $this->getRecord(), ...$this->getRedirectUrlParameters()]);
    }
}
