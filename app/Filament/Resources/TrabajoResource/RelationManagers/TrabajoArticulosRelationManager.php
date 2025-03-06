<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use App\Models\TrabajoArticulo;
use App\Services\FractionService;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TrabajoArticulosRelationManager extends RelationManager
{
    protected static string $relationship = 'trabajoArticulos';

    protected static ?string $title = 'Articulos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('articulo_nombre')
                    ->label('Artículo')
                    ->disabled()
                    ->columnSpanFull()
                    ->afterStateHydrated(function (TextInput $component, $record) {
                        $articulo = $record->articulo;
                        $categoria = $articulo->subCategoria->categoria->nombre;
                        $subCategoria = $articulo->subCategoria->nombre;
                        $especificacion = $articulo->especificacion ? " - {$articulo->especificacion}" : '';
                        $marca = $articulo->marca;
                        $color = $articulo->color ? " {$articulo->color}" : '';
                        $tamano_presentacion = $articulo->tamano_presentacion;

                        $nombreCompleto = "{$categoria} {$subCategoria}{$especificacion} - {$marca}{$color} - {$tamano_presentacion}";
                        $component->state($nombreCompleto);
                    }),
                TextInput::make('cantidad_fraccion')
                    ->label('Cantidad')
                    ->disabled() // Hace que el campo sea de solo lectura
                    ->afterStateHydrated(function (TextInput $component, $record) {
                        $cantidadFormateada = FractionService::decimalToFraction((float) $record->cantidad);
                        $component->state($cantidadFormateada);
                    }),
                TextInput::make('precio')
                    ->label('Precio para el servicio')
                    ->required()
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('articulo')
                    ->label('Artículo')
                    ->state(function (TrabajoArticulo $record) {
                        $articulo = $record->articulo;
                        $categoria = $articulo->subCategoria->categoria->nombre;
                        $subCategoria = $articulo->subCategoria->nombre;
                        $especificacion = $articulo->especificacion ? " - {$articulo->especificacion}" : '';
                        $marca = $articulo->marca;
                        $color = $articulo->color ? " {$articulo->color}" : '';
                        $tamano_presentacion = $articulo->tamano_presentacion;

                        $label = "{$categoria} {$subCategoria}{$especificacion} - {$marca}{$color} - {$tamano_presentacion}";
                        return $label;
                    }),
                TextColumn::make('precio')
                    ->label('Precio')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->alignCenter()
                    ->formatStateUsing(function ($state) {
                        return FractionService::decimalToFraction((float) $state);
                    }),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (TrabajoArticulo $record): string {
                        return number_format($record->precio * $record->cantidad, 2, '.', '');
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
            ]);
    }
}
