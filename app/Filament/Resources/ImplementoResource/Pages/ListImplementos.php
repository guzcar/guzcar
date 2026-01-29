<?php

namespace App\Filament\Resources\ImplementoResource\Pages;

use App\Filament\Resources\ImplementoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImplementos extends ListRecords
{
    protected static string $resource = ImplementoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}