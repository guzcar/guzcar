<?php

namespace App\Filament\Resources\ImplementoResource\Pages;

use App\Filament\Resources\ImplementoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateImplemento extends CreateRecord
{
    protected static string $resource = ImplementoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}