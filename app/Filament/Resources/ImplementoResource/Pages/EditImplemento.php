<?php

namespace App\Filament\Resources\ImplementoResource\Pages;

use App\Filament\Resources\ImplementoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImplemento extends EditRecord
{
    protected static string $resource = ImplementoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}