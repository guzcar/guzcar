<?php

namespace App\Filament\Resources\VentaResource\RelationManagers;

use App\Models\Articulo;
use App\Models\VentaArticulo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class VentaArticuloRelationManager extends RelationManager
{
    protected static string $relationship = 'ventaArticulos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('articulo_id')
                    ->label('Artículo')
                    ->columnSpanFull()
                    ->options(function () {
                        return Articulo::with(['subCategoria.categoria'])
                            ->get()
                            ->mapWithKeys(function ($articulo) {
                                $categoria = $articulo->subCategoria->categoria->nombre;
                                $subCategoria = $articulo->subCategoria->nombre;
                                $especificacion = $articulo->especificacion ? " - {$articulo->especificacion}" : '';
                                $marca = $articulo->marca;
                                $tamanoPresentacion = $articulo->tamano_presentacion;

                                $label = "{$categoria} {$subCategoria}{$especificacion} - {$marca} - {$tamanoPresentacion}";

                                return [$articulo->id => $label];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($articulo = Articulo::find($state)) {
                            $set('precio', $articulo->precio ?? $articulo->costo);
                            $set('min_precio', $articulo->costo);
                        }
                    }),
                TextInput::make('precio')
                    ->label('Precio de venta')
                    ->required()
                    ->numeric()
                    ->minValue(fn (Forms\Get $get) => $get('min_precio') ?? 0)
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->dehydrated(),
                TextInput::make('cantidad')
                    ->default(1)
                    ->required()
                    ->numeric()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('precio')
            ->columns([
                TextColumn::make('articulo')
                    ->label('Artículo')
                    ->state(function (VentaArticulo $record) {

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
                TextColumn::make('articulo.ubicaciones.codigo')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->wrap()
                    ->badge(),
                TextColumn::make('cantidad')
                    ->alignCenter(),
                TextColumn::make('precio')
                    ->prefix('S/ ')
                    ->alignRight(),
                TextColumn::make('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (VentaArticulo $record): string {
                        return number_format($record->precio * $record->cantidad, 2, '.', '');
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
