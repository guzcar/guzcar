<?php

namespace App\Filament\Resources\ArticuloPresentacionResource\Pages;

use App\Filament\Resources\ArticuloPresentacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticuloPresentacion extends EditRecord
{
    protected static string $resource = ArticuloPresentacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
