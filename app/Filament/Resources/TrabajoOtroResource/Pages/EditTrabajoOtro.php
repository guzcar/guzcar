<?php

namespace App\Filament\Resources\TrabajoOtroResource\Pages;

use App\Filament\Resources\TrabajoOtroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajoOtro extends EditRecord
{
    protected static string $resource = TrabajoOtroResource::class;

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
