<?php

namespace App\Filament\Resources\DespachoResource\Pages;

use App\Filament\Resources\DespachoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDespacho extends EditRecord
{
    protected static string $resource = DespachoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadPdf')
                ->label('Descargar')
                ->url(route('despachos.pdf', $this->record->id))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['responsable'] = $this->record->responsable->name;
        return $data;
    }
}
