<?php

namespace App\Filament\Resources\TrabajoOtroResource\Pages;

use App\Filament\Resources\TrabajoOtroResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrabajoOtro extends CreateRecord
{
    protected static string $resource = TrabajoOtroResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
