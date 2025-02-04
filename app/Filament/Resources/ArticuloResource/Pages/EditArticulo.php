<?php

namespace App\Filament\Resources\ArticuloResource\Pages;

use App\Filament\Resources\ArticuloResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticulo extends EditRecord
{
    protected static string $resource = ArticuloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillForm(): void
    {
        $data = $this->getRecord()->attributesToArray();

        $articulo = $this->getRecord();
        $subCategoria = $articulo->subCategoria;
        $articuloUbicaciones = $articulo->ubicaciones()->with('ubicacion.almacen')->get();

        $data['sub_categoria_id'] = $articulo->sub_categoria_id;
        $data['categoria_id'] = $subCategoria->categoria_id;
        
        $data['ubicaciones'] = [
            [
                'almacen_id' => 1,
                'ubicacion_id' => 10,
            ],
            [
                'almacen_id' => 2,
                'ubicacion_id' => 20,
            ],
        ];

        $this->fillFormWithDataAndCallHooks($articulo, $data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['ubicaciones'] = [
            [
                'almacen_id' => 1,
                'ubicacion_id' => 10,
            ],
            [
                'almacen_id' => 2,
                'ubicacion_id' => 20,
            ],
        ];
        return $data;
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     dd($data);
    // }
}
