<?php

namespace App\Filament\Resources\MaletaResource\Pages;

use App\Filament\Resources\MaletaResource;
use App\Models\Maleta;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaleta extends EditRecord
{
    protected static string $resource = MaletaResource::class;

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
