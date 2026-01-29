<?php

namespace App\Filament\Resources\ImplementoEntradaResource\Pages;

use App\Filament\Resources\ImplementoEntradaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateImplementoEntrada extends CreateRecord
{
    protected static string $resource = ImplementoEntradaResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}