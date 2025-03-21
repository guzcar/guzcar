<?php

namespace App\Filament\Resources\SalidaResource\Pages;

use App\Filament\Resources\SalidaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalida extends EditRecord
{
    protected static string $resource = SalidaResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        // Obtener el artículo asociado
        $articulo = $record->articulo;

        // Cargar datos básicos
        $data['responsable'] = $record->responsable->name;
        $data['stock'] = $articulo->stock;
        $data['abiertos'] = $articulo->abiertos;

        if ($articulo) {
            $data['fraccionable'] = $articulo->fraccionable;
            $data['abiertos'] = $articulo->fraccionable ? $articulo->abiertos : null;

            // Obtener la cantidad y el movimiento
            $cantidad = ceil($record->cantidad);
            $movimiento = $record->movimiento;

            // Ajustar stock y abiertos según el movimiento
            switch ($movimiento) {
                case 'consumo_completo':
                    // Sumar la cantidad al stock (ya que se está editando)
                    $data['stock'] += $cantidad;
                    break;

                case 'abrir_nuevo':
                    // Sumar 1 al stock (ya que se está editando)
                    $data['stock'] += 1;
                    // Restar 1 a los abiertos (ya que se está editando)
                    $data['abiertos'] -= 1;
                    break;

                case 'terminar_abierto':
                    // Sumar 1 a los abiertos (ya que se está editando)
                    $data['abiertos'] += 1;
                    break;

                case 'consumo_parcial':
                    // No se ajusta stock ni abiertos en consumo parcial
                    break;
            }

            // Manejar la cantidad fraccionada
            if ($articulo->fraccionable) {
                if (in_array($cantidad, [0.25, 0.50, 0.75, 1])) {
                    $data['cantidad_fraccion'] = $cantidad;
                    $data['cantidad_custom'] = null;
                } else {
                    $data['cantidad_fraccion'] = 'custom';
                    $data['cantidad_custom'] = $cantidad;
                }
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
