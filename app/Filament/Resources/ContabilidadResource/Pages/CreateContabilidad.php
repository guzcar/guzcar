<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContabilidad extends CreateRecord
{
    protected static string $resource = ContabilidadResource::class;

    protected array $comprobanteIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->comprobanteIds = collect($data['comprobantes'] ?? [])
            ->pluck('comprobante_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        unset($data['comprobantes']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->comprobantes()->sync($this->comprobanteIds);
    }
}
