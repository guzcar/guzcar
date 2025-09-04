<?php

namespace App\Filament\Resources\ContabilidadResource\Pages;

use App\Filament\Resources\ContabilidadResource;
use App\Models\Contabilidad;
use App\Models\Trabajo;
use App\Services\TrabajoService;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContabilidad extends EditRecord
{
    protected static string $resource = ContabilidadResource::class;

    protected array $comprobanteIds = [];

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),
            ActionGroup::make([

                Action::make('Descargar presupuesto')
                    ->icon('heroicon-s-document-currency-dollar')
                    ->url(
                        fn(Contabilidad $trabajo): string => route('trabajo.pdf.presupuesto', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),

                Action::make('Descargar proforma')
                    ->icon('heroicon-s-document')
                    ->url(
                        fn(Contabilidad $trabajo): string => route('trabajo.pdf.proforma', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),
            ])
                ->button()
                ->label('Descargar')
                ->icon('heroicon-s-arrow-down-tray'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
    {
        $this->record->comprobantes()->sync($this->comprobanteIds);
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        TrabajoService::actualizarTrabajoPorId($record);
        return $record;
    }
}
