<?php

namespace App\Filament\Resources\ArticuloGrupoResource\Pages;

use App\Filament\Resources\ArticuloGrupoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;

class ManageArticuloGrupos extends ManageRecords
{
    protected static string $resource = ArticuloGrupoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::Medium),
        ];
    }
}
