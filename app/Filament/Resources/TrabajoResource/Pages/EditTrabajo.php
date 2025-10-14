<?php

namespace App\Filament\Resources\TrabajoResource\Pages;

use App\Filament\Resources\TrabajoResource;
use App\Models\Trabajo;
use App\Services\TrabajoService;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTrabajo extends EditRecord
{
    protected static string $resource = TrabajoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
            Actions\ViewAction::make()
                ->icon('heroicon-o-eye'),

            // TODO: Buscar una mejor terminologia
            Action::make('Ver Inventario')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->label('Inventario')
                ->action(function () {
                    // 1. Guarda los datos del formulario (esto no cambia).
                    $this->save();

                    // 2. ✅ Redirige a tu ruta nombrada de Laravel.
                    return redirect()->route('admin.trabajos.inventario', [
                        'trabajo' => $this->getRecord()
                    ]);
                }),

            ActionGroup::make([
                Action::make('Descargar Check List')
                    ->url(
                        fn(Trabajo $trabajo): string => route('pdf.admin.inventario.ingreso', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    ),

                Action::make('Descargar informe')
                    ->icon('heroicon-s-document-text')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.informe', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    )
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::informe')),

                Action::make('Descargar evidencias')
                    ->icon('heroicon-s-photo')
                    ->url(
                        fn(Trabajo $trabajo): string => route('trabajo.pdf.evidencia', ['trabajo' => $trabajo]),
                        shouldOpenInNewTab: true
                    )
                    ->hidden(fn() => !auth()->user()->can('view_evidencia')),
            ])
                ->button()
                ->label('Descargar')
                ->icon('heroicon-s-arrow-down-tray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Configura la acción del botón de cancelar para que redirija al index.
     */
    protected function getCancelFormAction(): Actions\Action
    {
        return Actions\Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->url($this->getResource()::getUrl('index'))
            ->color('gray');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        TrabajoService::actualizarTrabajoPorId($record);
        return $record;
    }
}
