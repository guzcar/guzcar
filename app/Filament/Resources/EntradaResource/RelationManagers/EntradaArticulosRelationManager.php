<?php

namespace App\Filament\Resources\EntradaResource\RelationManagers;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\EntradaArticulo;
use App\Models\SubCategoria;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
                        $articulo = Articulo::find($state);
                        if ($articulo) {
                            $set('costo', $articulo->costo);
                        }
                    })
                    ->createOptionForm([
                        Select::make('categoria_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Categoría')
                            ->options(Categoria::all()->pluck('nombre', 'id'))
                            ->reactive()
                            ->createOptionForm([
                                TextInput::make('nombre')
                                    ->label('Nombre de la categoría')
                                    ->required(),
                            ])
                            ->createOptionUsing(function ($data) {
                                $categoria = Categoria::create([
                                    'nombre' => $data['nombre'],
                                ]);
                                return $categoria->id;
                            })
                            ->afterStateUpdated(fn($state, callable $set) => $set('sub_categoria_id', null)),
                        Select::make('sub_categoria_id')
                            ->required()
                            ->label('Subcategoría')
                            ->placeholder(placeholder: '')
                            ->searchable()
                            ->options(function ($get) {
                                $categoriaId = $get('categoria_id');
                                if ($categoriaId) {
                                    return SubCategoria::where('categoria_id', $categoriaId)
                                        ->pluck('nombre', 'id')
                                        ->toArray();
                                }
                                return [];
                            })
                            ->createOptionForm([
                                TextInput::make('nombre')
                                    ->label('Nombre de la subcategoría')
                                    ->required(),
                            ])
                            ->createOptionUsing(function ($data, $get) {
                                $categoriaId = $get('categoria_id');
                                if (!$categoriaId) {
                                    throw new \Exception('Primero seleccione una categoría.');
                                }

                                $subCategoria = SubCategoria::create([
                                    'nombre' => $data['nombre'],
                                    'categoria_id' => $categoriaId,
                                ]);
                                return $subCategoria->id;
                            })
                            ->disabled(fn($get) => !$get('categoria_id')),
                        TextInput::make('especificacion')
                            ->label('Especificación'),
                        TextInput::make('marca')
                            ->required(),
                        Grid::make()
                            ->schema([
                                TextInput::make('tamano_presentacion')
                                    ->label('Tamaño / Presentación')
                                    ->required(),
                                TextInput::make('costo')
                                    ->label('Costo de compra')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95),
                            ])
                            ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                        Textarea::make('descripcion')
                            ->label('Descripción'),
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
                    ->required()
                    ->numeric()
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
