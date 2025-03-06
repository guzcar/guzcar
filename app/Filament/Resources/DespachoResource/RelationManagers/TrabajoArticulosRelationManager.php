<?php

namespace App\Filament\Resources\DespachoResource\RelationManagers;

use App\Models\Articulo;
use App\Models\TrabajoArticulo;
use App\Services\FractionService;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
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

class TrabajoArticulosRelationManager extends RelationManager
{
    protected static string $relationship = 'trabajoArticulos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('articulo_id')
                    ->label('Artículo')
                    ->required()
                    ->options(function () {
                        return Articulo::with(['subCategoria.categoria'])
                            ->get()
                            ->mapWithKeys(function ($articulo) {
                                $categoria = $articulo->subCategoria->categoria->nombre;
                                $subCategoria = $articulo->subCategoria->nombre;
                                $especificacion = $articulo->especificacion ? " - {$articulo->especificacion}" : '';
                                $marca = $articulo->marca;
                                $color = $articulo->color ? " {$articulo->color}" : '';
                                $tamano_presentacion = $articulo->tamano_presentacion;

                                $label = "{$categoria} {$subCategoria}{$especificacion} - {$marca}{$color} - {$tamano_presentacion}";

                                return [$articulo->id => $label];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $articulo = Articulo::find($state);
                        if ($articulo) {
                            $set('precio', $articulo->costo);
                            $set('stock', $articulo->stock);
                            $set('fraccionable', $articulo->fraccionable);

                            if ($articulo->fraccionable) {
                                $set('abiertos', $articulo->abiertos);
                            } else {
                                $set('abiertos', null);
                            }
                        }
                    }),
                TextInput::make('precio')
                    ->label('Costo para el servicio')
                    ->required()
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->dehydrated(),
                Grid::make()
                    ->schema(function ($get) {
                        $fraccionable = $get('fraccionable');
                        if ($fraccionable) {
                            return [
                                Select::make('cantidad_fraccion')
                                    ->label('Cantidad fraccionada')
                                    ->options([
                                        '0.25' => '1/4',
                                        '0.50' => '1/2',
                                        '0.75' => '3/4',
                                        '1.00' => '1',
                                        'custom' => 'Ingresar valor exacto',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state !== 'custom') {
                                            $set('cantidad', $state);
                                            $set('cantidad_custom', null);
                                        }
                                    }),
                                TextInput::make('cantidad_custom')
                                    ->label('Cantidad exacta')
                                    ->numeric()
                                    ->hidden(fn($get) => $get('cantidad_fraccion') !== 'custom')
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state !== null) {
                                            $set('cantidad', $state);
                                        }
                                    }),
                                Hidden::make('cantidad'),
                            ];
                        } else {
                            return [
                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric(),
                            ];
                        }
                    }),
                Select::make('movimiento')
                    ->label('Movimiento')
                    ->options([
                        'cerrado' => 'Abrir y gastar',
                        'abierto' => 'Gastar abierto',
                    ])
                    ->default('cerrado')
                    ->required()
                    ->placeholder('')
                    ->hidden(fn($get) => !$get('articulo_id') || !Articulo::find($get('articulo_id'))?->fraccionable),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Salidas')
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
                TextColumn::make('articulo.ubicaciones.codigo')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->wrap()
                    ->badge(),
                TextColumn::make('cantidad')
                    ->alignCenter()
                    ->formatStateUsing(function ($state) {
                        return FractionService::decimalToFraction((float) $state);
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar salida'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->fillForm(function (TrabajoArticulo $record) {
                        // Datos base que siempre se pasan al formulario
                        $data = [
                            'articulo_id' => $record->articulo_id,
                            'precio' => $record->precio,
                            'cantidad' => $record->cantidad,
                            'movimiento' => $record->movimiento,
                            'fraccionable' => $record->articulo->fraccionable,
                        ];

                        // Si el artículo es fraccionable, configuramos los campos de fracción
                        if ($record->articulo->fraccionable) {
                            $cantidad = $record->cantidad;

                            if (in_array($cantidad, [0.25, 0.50, 0.75, 1])) {
                                $data['cantidad_fraccion'] = $cantidad;
                                $data['cantidad_custom'] = null;
                            } else {
                                $data['cantidad_fraccion'] = 'custom';
                                $data['cantidad_custom'] = $cantidad;
                            }
                        }

                        return $data;
                    }),
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
