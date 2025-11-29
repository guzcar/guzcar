<?php

namespace App\Filament\Resources\CronogramaTareaResource\Pages;

use App\Filament\Resources\CronogramaTareaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCronogramaTareas extends ManageRecords
{
    protected static string $resource = CronogramaTareaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
