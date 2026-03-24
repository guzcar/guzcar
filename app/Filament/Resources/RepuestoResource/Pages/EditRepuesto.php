<?php

namespace App\Filament\Resources\RepuestoResource\Pages;

use App\Filament\Resources\RepuestoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepuesto extends EditRecord
{
    protected static string $resource = RepuestoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
