<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages;
use App\Filament\Resources\ArticuloResource\RelationManagers;
use App\Models\Almacen;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\SubCategoria;
use App\Models\Ubicacion;
use App\Services\FractionService;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Artículo';

    protected static ?string $pluralModelLabel = 'Artículos';

    protected static ?string $navigationLabel = 'Artículos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
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
                                    ->placeholder('Seleccione una subcategoría')
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
                                Grid::make()
                                    ->schema([
                                        TextInput::make('especificacion')
                                            ->label('Especificación'),
                                        TextInput::make('marca')
                                            ->required(),
                                        TextInput::make('tamano_presentacion')
                                            ->label('Tamaño / Presentación')
                                            ->required(),
                                        TextInput::make('color'),
                                    ])->columns(2),
                                Grid::make()
                                    ->schema([
                                        TextInput::make('costo')
                                            ->label('Costo de compra')
                                            ->required()
                                            ->numeric()
                                            ->prefix('S/ ')
                                            ->maxValue(42949672.95),
                                        TextInput::make('precio')
                                            ->label('Precio de venta')
                                            ->numeric()
                                            ->prefix('S/ ')
                                            ->maxValue(42949672.95),
                                    ])
                                    ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                                Textarea::make('descripcion')
                                    ->label('Descripción'),
                            ])
                            ->heading('Artículo')
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 1, 'sm' => 1]),
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Repeater::make('articuloUbicaciones')
                                            ->label('Ubicaciones')
                                            ->relationship('articuloUbicaciones')
                                            ->defaultItems(0)
                                            ->addActionLabel('Añadir ubicación')
                                            ->simple(
                                                Select::make('ubicacion_id')
                                                    ->relationship('ubicacion', 'codigo', fn($query) => $query->withTrashed())
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm([
                                                        TextInput::make('codigo')
                                                            ->label('Código')
                                                            ->required()
                                                            ->unique(ignoreRecord: true)
                                                            ->maxLength(10),
                                                    ])
                                                    ->editOptionForm([
                                                        TextInput::make('codigo')
                                                            ->label('Código')
                                                            ->required()
                                                            ->unique(ignoreRecord: true)
                                                            ->maxLength(10),
                                                    ])
                                            )
                                    ])
                                    ->heading('Ubicación en Almacen'),
                                Section::make()
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                Toggle::make('fraccionable')
                                                    ->inline(false),
                                                TextInput::make('stock')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0),
                                                TextInput::make('abiertos')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0),
                                                TextInput::make('mermas')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(0),
                                            ])
                                            ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2])
                                    ])
                                    ->heading('Inventario'),
                            ])
                            ->columnSpan(['xl' => 2, 'lg' => 2, 'md' => 1, 'sm' => 1]),
                    ])
                    ->columns(['xl' => 5, 'lg' => 5, 'md' => 1, 'sm' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subCategoria.categoria.nombre')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('subCategoria.nombre')
                    ->label('Sub-Categoría')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('especificacion')
                    ->label('Especificación')
                    ->placeholder('Sin especificación')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('marca')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tamano_presentacion')
                    ->label('Presentación')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('color')
                    ->placeholder('Sin color')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('ubicaciones.codigo')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->searchable()
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('costo')
                    ->label('Costo de compra')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('precio')
                    ->label('Precio de venta')
                    ->prefix('S/ ')
                    ->placeholder('S/ 0.00')
                    ->alignRight()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('stock')
                    ->alignCenter(),
                TextColumn::make('abiertos')
                    ->alignCenter()
                    ->formatStateUsing(function ($state, FractionService $fractionService) {
                        return $fractionService->decimalToFraction((float) $state);
                    }),
                TextColumn::make('mermas')
                    ->alignCenter(),
                TextColumn::make('descripcion')
                    ->placeholder('Sin descripción')
                    ->extraAttributes(['style' => 'width: 15rem'])
                    ->lineClamp(2)
                    ->wrap()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de edición')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                ValueRangeFilter::make('costo')
                    ->label('Costo de compra'),
                ValueRangeFilter::make('precio')
                    ->label('Precio de venta'),
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticulos::route('/'),
            'create' => Pages\CreateArticulo::route('/create'),
            'edit' => Pages\EditArticulo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
