<?php

namespace App\Filament\Resources\EntradaResource\RelationManagers;

use App\Models\Articulo;
use App\Models\ArticuloCategoria;
use App\Models\ArticuloMarca;
use App\Models\ArticuloPresentacion;
use App\Models\ArticuloSubCategoria;
use App\Models\ArticuloUnidad;
use App\Models\Categoria;
use App\Models\EntradaArticulo;
use App\Models\SubCategoria;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntradaArticulosRelationManager extends RelationManager
{
    protected static string $relationship = 'entradaArticulos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('articulo_id')
                    ->label('Artículo')
                    ->columnSpanFull()
                    ->options(function () {
                        return Articulo::with(['categoria', 'subCategoria', 'marca', 'unidad', 'presentacion'])
                            ->get()
                            ->mapWithKeys(function ($articulo) {
                                $categoria = $articulo->categoria->nombre ?? null;
                                $marca = $articulo->marca->nombre ?? null;
                                $subCategoria = $articulo->subCategoria->nombre ?? null;
                                $especificacion = $articulo->especificacion ?? null;
                                $presentacion = $articulo->presentacion->nombre ?? null;
                                $medida = $articulo->medida ?? null;
                                $unidad = $articulo->unidad->nombre ?? null;
                                $color = $articulo->color ?? null;

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

                                $label = implode(' ', $labelParts);

                                return [$articulo->id => $label];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $articulo = Articulo::find($state);
                        if ($articulo) {
                            $set('costo', $articulo->costo);
                        }
                    })
                    ->createOptionForm([
                        Grid::make()
                            ->schema([
                                Select::make('categoria_id')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Artículo')
                                    ->options(ArticuloCategoria::all()->pluck('nombre', 'id'))
                                    ->reactive()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la categoría')
                                            ->required()
                                            ->unique('articulo_categorias', 'nombre'),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return ArticuloCategoria::create([
                                            'nombre' => $data['nombre'],
                                        ])->id;
                                    })
                                    ->afterStateUpdated(fn($state, callable $set) => $set('sub_categoria_id', null)),
                                Select::make('marca_id')
                                    ->label('Marca')
                                    ->searchable()
                                    ->preload()
                                    ->options(ArticuloMarca::all()->pluck('nombre', 'id'))
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la marca')
                                            ->required()
                                            ->unique('articulo_marcas', 'nombre'),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return ArticuloMarca::create([
                                            'nombre' => $data['nombre'],
                                        ])->id;
                                    }),
                                Select::make('sub_categoria_id')
                                    ->label('Grado / Número')
                                    ->searchable()
                                    ->options(function ($get) {
                                        $categoriaId = $get('categoria_id');
                                        if ($categoriaId) {
                                            return ArticuloSubCategoria::where('categoria_id', $categoriaId)
                                                ->pluck('nombre', 'id')
                                                ->toArray();
                                        }
                                        return [];
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la subcategoría')
                                            ->required(),
                                            // ->unique('articulo_sub_categorias', 'nombre'),
                                        Hidden::make('categoria_id')
                                            ->default(fn(Get $get) => $get('categoria_id')),
                                    ])
                                    ->createOptionUsing(function ($data, $get) {
                                        $categoriaId = $get('categoria_id');
                                        if (!$categoriaId) {
                                            throw new \Exception('Primero seleccione una categoría.');
                                        }

                                        return ArticuloSubCategoria::create([
                                            'nombre' => $data['nombre'],
                                            'categoria_id' => $categoriaId,
                                        ])->id;
                                    })
                                    ->disabled(fn($get) => !$get('categoria_id')),
                                TextInput::make('especificacion')
                                    ->label('Especificación')
                                    ->nullable(),
                                Select::make('presentacion_id')
                                    ->label('Presentación')
                                    ->searchable()
                                    ->preload()
                                    ->options(ArticuloPresentacion::all()->pluck('nombre', 'id'))
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la presentación')
                                            ->required()
                                            ->unique('articulo_presentaciones', 'nombre'),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return ArticuloPresentacion::create([
                                            'nombre' => $data['nombre'],
                                        ])->id;
                                    }),
                                Grid::make()
                                    ->schema([
                                        TextInput::make('medida')
                                            ->label('Medida')
                                            ->numeric()
                                            ->nullable()
                                            ->columnSpan(['default' => 1, 'sm' => 1, 'md' => 1, 'lg' => 1, 'xl' => 1, '2xl' => 1]),
                                        Select::make('unidad_id')
                                            ->label('Unidad')
                                            ->placeholder('')
                                            ->searchable()
                                            ->preload()
                                            ->options(ArticuloUnidad::all()->pluck('nombre', 'id'))
                                            ->createOptionForm([
                                                TextInput::make('nombre')
                                                    ->label('Nombre de la unidad')
                                                    ->required()
                                                    ->unique('articulo_unidades', 'nombre'),
                                            ])
                                            ->createOptionUsing(function ($data) {
                                                return ArticuloUnidad::create([
                                                    'nombre' => $data['nombre'],
                                                ])->id;
                                            })
                                            ->columnSpan(['default' => 1, 'sm' => 1, 'md' => 1, 'lg' => 1, 'xl' => 1, '2xl' => 1]),
                                    ])
                                    ->columnSpan(['default' => 1, 'sm' => 1, 'md' => 1, 'lg' => 1, 'xl' => 1, '2xl' => 1])
                                    ->columns(['default' => 2, 'sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2]),
                                TextInput::make('color')
                                    ->label('Color')
                                    ->nullable(),
                                TextInput::make('costo')
                                    ->label('Costo de compra')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95),
                                Textarea::make('descripcion')
                                    ->columnSpanFull()
                                    ->label('Descripción')
                                    ->nullable(),
                            ])
                            ->columns(2)
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Articulo::create($data)->getKey();
                    }),

                TextInput::make('costo')
                    ->label('Costo de compra')
                    ->required()
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->dehydrated(),

                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->default(1)
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('articulo')
                    ->label('Artículo')
                    ->state(function (EntradaArticulo $record) {
                        $articulo = $record->articulo;

                        $categoria = $articulo->categoria->nombre ?? null;
                        $marca = $articulo->marca->nombre ?? null;
                        $subCategoria = $articulo->subCategoria->nombre ?? null;
                        $especificacion = $articulo->especificacion ?? null;
                        $presentacion = $articulo->presentacion->nombre ?? null;
                        $medida = $articulo->medida ?? null;
                        $unidad = $articulo->unidad->nombre ?? null;
                        $color = $articulo->color ?? null;

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

                        $label = implode(' ', $labelParts);

                        return $label;
                    }),
                TextColumn::make('articulo.ubicaciones.codigo')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->wrap()
                    ->badge(),
                TextColumn::make('cantidad')
                    ->alignCenter(),
                TextColumn::make('costo')
                    ->prefix('S/ ')
                    ->alignRight(),
                TextColumn::make('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (EntradaArticulo $record): string {
                        return number_format($record->costo * $record->cantidad, 2, '.', '');
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Entrada de artículo'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
