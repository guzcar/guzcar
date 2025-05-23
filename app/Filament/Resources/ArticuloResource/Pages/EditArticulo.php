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

    // protected function fillForm(): void
    // {
    //     $data = $this->getRecord()->attributesToArray();

    //     $articulo = $this->getRecord();
    //     $subCategoria = $articulo->subCategoria;

    //     $data['sub_categoria_id'] = $articulo->sub_categoria_id;
    //     $data['categoria_id'] = $subCategoria->categoria_id;

    //     $this->fillFormWithDataAndCallHooks($articulo, $data);
    // }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
