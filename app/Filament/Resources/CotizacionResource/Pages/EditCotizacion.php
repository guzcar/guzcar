<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use App\Models\Cotizacion;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCotizacion extends EditRecord
{
    protected static string $resource = CotizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn(Cotizacion $record) => route('cotizaciones.pdf', $record))
                    ->openUrlInNewTab(),
        ];
    }
}
