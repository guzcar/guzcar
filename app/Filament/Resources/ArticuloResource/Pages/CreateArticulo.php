<?php

namespace App\Filament\Resources\ArticuloResource\Pages;

use App\Filament\Resources\ArticuloResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticulo extends CreateRecord
{
    protected static string $resource = ArticuloResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        return $data;
    }
}
