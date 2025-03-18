<?php

namespace App\Filament\Resources\DespachoResource\RelationManagers;

use App\Models\Articulo;
use App\Models\TrabajoArticulo;
use App\Services\FractionService;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
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
                // Selección de artículo (deshabilitado en edición)
                Select::make('articulo_id')
                    ->label('Artículo')
                    ->required()
                    ->options(function () {
                        return Articulo::with(['subCategoria.categoria', 'marca', 'unidad', 'presentacion'])
                            ->get()
                            ->mapWithKeys(function ($articulo) {
                                $labelParts = array_filter([
                                    $articulo->categoria->nombre ?? null,
                                    $articulo->marca->nombre ?? null,
                                    $articulo->subCategoria->nombre ?? null,
                                    $articulo->especificacion ?? null,
                                    $articulo->presentacion->nombre ?? null,
                                    $articulo->medida ?? null,
                                    $articulo->unidad->nombre ?? null,
                                    $articulo->color ?? null,
                                ]);
                                $label = implode(' ', $labelParts);
                                return [$articulo->id => $label];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->disabled(fn($context) => $context === 'edit') // Deshabilitar en edición
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $articulo = Articulo::find($state);
                        if ($articulo) {
                            $set('stock', $articulo->stock);
                            $set('precio', $articulo->precio);
                            $set('fraccionable', $articulo->fraccionable);
                            $set('abiertos', $articulo->fraccionable ? $articulo->abiertos : null);

                            // Imprimir stock y abiertos (si es fraccionable)
                            if ($articulo->fraccionable) {
                                $set('abiertos_info', "Stock: {$articulo->stock}, Abiertos: {$articulo->abiertos}");
                            } else {
                                $set('abiertos_info', "Stock: {$articulo->stock}");
                            }

                            // Reiniciar el movimiento si el artículo no es fraccionable
                            if (!$articulo->fraccionable) {
                                $set('movimiento', 'consumo_completo');
                            }
                        }
                    }),

                // Precio
                TextInput::make('precio')
                    ->label('Costo para el servicio')
                    ->required()
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->dehydrated(),

                // Movimiento (solo para artículos fraccionables)
                Select::make('movimiento')
                    ->label('Movimiento')
                    ->options([
                        'consumo_completo' => 'Consumo completo',
                        'abrir_nuevo' => 'Abrir nuevo',
                        'terminar_abierto' => 'Terminar abierto',
                        'consumo_parcial' => 'Consumo parcial',
                    ])
                    ->default('consumo_completo')
                    ->required()
                    ->placeholder('')
                    ->hidden(fn($get) => !$get('articulo_id') || !Articulo::find($get('articulo_id'))?->fraccionable)
                    ->reactive(),

                // Cantidad (fraccionada o entera)
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
                                    ->required()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state !== null) {
                                            $set('cantidad', $state);
                                        }
                                    }),
                                Hidden::make('cantidad')
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $movimiento = $get('movimiento');
                                                $stockDisponible = $get('stock');
                                                $abiertos = $get('abiertos');

                                                switch ($movimiento) {
                                                    case 'consumo_completo':
                                                        if ($value > $stockDisponible) {
                                                            $fail("La cantidad no puede ser mayor al stock disponible ($stockDisponible).");
                                                        }
                                                        break;
                                                    case 'abrir_nuevo':
                                                        if ($value >= 1) {
                                                            $fail("La cantidad debe ser menor a 1.");
                                                        } elseif ($stockDisponible < 1) {
                                                            $fail("No hay suficiente stock para abrir un nuevo artículo.");
                                                        }
                                                        break;
                                                    case 'terminar_abierto':
                                                        if ($abiertos < 1) {
                                                            $fail("No hay artículos abiertos para terminar.");
                                                        }
                                                        break;
                                                    case 'consumo_parcial':
                                                        if ($value >= 1) {
                                                            $fail("La cantidad debe ser menor a 1.");
                                                        } elseif ($abiertos < 1) {
                                                            $fail("No hay artículos abiertos para gastar.");
                                                        }
                                                        break;
                                                }
                                            };
                                        },
                                    ]),
                            ];
                        } else {
                            return [
                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $stockDisponible = $get('stock');
                                                if ($value > $stockDisponible) {
                                                    $fail("La cantidad no puede ser mayor al stock disponible ($stockDisponible).");
                                                }
                                            };
                                        },
                                    ]),
                            ];
                        }
                    }),

                // Mostrar stock y abiertos (si es fraccionable)
                Placeholder::make('abiertos_info')
                    ->label('Información de stock')
                    ->content(fn($get) => $get('abiertos_info') ?? 'Seleccione un artículo'),
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

                        // Campos que se concatenarán en el label
                        $categoria = $articulo->categoria->nombre ?? null; // Acceder directamente a la categoría
                        $marca = $articulo->marca->nombre ?? null; // Acceder al nombre de la marca
                        $subCategoria = $articulo->subCategoria->nombre ?? null;
                        $especificacion = $articulo->especificacion ?? null;
                        $presentacion = $articulo->presentacion->nombre ?? null; // Acceder al nombre de la presentación
                        $medida = $articulo->medida ?? null;
                        $unidad = $articulo->unidad->nombre ?? null; // Acceder al nombre de la unidad
                        $color = $articulo->color ?? null;

                        // Construye el label dinámicamente
                        $labelParts = [];
                        if ($categoria)
                            $labelParts[] = $categoria;
                        if ($marca)
                            $labelParts[] = $marca;
                        if ($subCategoria)
                            $labelParts[] = $subCategoria;
                        if ($especificacion)
                            $labelParts[] = $especificacion;
                        if ($presentacion)
                            $labelParts[] = $presentacion;
                        if ($medida)
                            $labelParts[] = $medida;
                        if ($unidad)
                            $labelParts[] = $unidad;
                        if ($color)
                            $labelParts[] = $color;

                        // Une las partes con un espacio
                        $label = implode(' ', $labelParts);

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
                    ->mutateRecordDataUsing(function (array $data, TrabajoArticulo $record): array {
                        // Lógica para ajustar datos antes de la edición (similar a EditSalida)
                        $articulo = $record->articulo;
                        if ($articulo) {
                            // Cargar datos básicos
                            $data['stock'] = $articulo->stock;
                            $data['abiertos'] = $articulo->fraccionable ? $articulo->abiertos : null;

                            // Ajustar stock y abiertos según el movimiento
                            $cantidad = $record->cantidad;
                            $movimiento = $record->movimiento;

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

                            // Imprimir stock y abiertos (si es fraccionable)
                            if ($articulo->fraccionable) {
                                $data['abiertos_info'] = "Stock: {$data['stock']}, Abiertos: {$data['abiertos']}";
                            } else {
                                $data['abiertos_info'] = "Stock: {$data['stock']}";
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
