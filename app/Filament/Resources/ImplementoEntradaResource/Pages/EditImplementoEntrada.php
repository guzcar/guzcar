<?php

namespace App\Filament\Resources\ImplementoEntradaResource\Pages;

use App\Filament\Resources\ImplementoEntradaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImplementoEntrada extends EditRecord
{
    protected static string $resource = ImplementoEntradaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;
        $data['responsable_nombre'] = $record->responsable?->name;
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}