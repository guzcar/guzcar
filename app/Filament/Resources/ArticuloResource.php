<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages;
use App\Filament\Resources\ArticuloResource\RelationManagers;
use App\Models\Almacen;
use App\Models\Articulo;
use App\Models\ArticuloCategoria;
use App\Models\ArticuloSubCategoria;
use App\Models\Categoria;
use App\Models\SubCategoria;
use App\Models\Ubicacion;
use App\Services\FractionService;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;
use function PHPUnit\Framework\isInfinite;

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 70;

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
                                    ->label('Artículo')
                                    ->relationship('categoria', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('sub_categoria_id', null);
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la categoría')
                                            ->required()
                                            ->unique('articulo_categorias', 'nombre'),
                                    ]),
                                Select::make('marca_id')
                                    ->label('Marca')
                                    ->relationship('marca', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la marca')
                                            ->required()
                                            ->unique('articulo_marcas', 'nombre'),
                                    ]),
                                Select::make('sub_categoria_id')
                                    ->label('Grado / Número')
                                    ->options(function (Get $get) {
                                        $categoriaId = $get('categoria_id');
                                        if (!$categoriaId) {
                                            return [];
                                        }
                                        return ArticuloSubCategoria::where('categoria_id', $categoriaId)
                                            ->pluck('nombre', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la subcategoría')
                                            ->required(),
                                        // ->unique('articulo_sub_categorias', 'nombre'),
                                        Hidden::make('categoria_id')
                                            ->default(fn(Get $get) => $get('categoria_id')),
                                    ])
                                    ->createOptionUsing(function (array $data, Get $get) {
                                        return ArticuloSubCategoria::create([
                                            'nombre' => $data['nombre'],
                                            'categoria_id' => $get('categoria_id'),
                                        ])->id;
                                    })
                                    // ->editOptionForm([
                                    //     TextInput::make('nombre')
                                    //         ->label('Nombre de la subcategoría')
                                    //         ->required()
                                    //         ->unique('articulo_sub_categorias', 'nombre'),
                                    // ])
                                    ->disabled(fn(Get $get) => !$get('categoria_id')),
                                TextInput::make('especificacion')
                                    ->label('Especificación')
                                    ->nullable(),
                                Select::make('presentacion_id')
                                    ->label('Presentación')
                                    ->relationship('presentacion', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre de la presentación')
                                            ->required()
                                            ->unique('articulo_presentaciones', 'nombre'),
                                    ]),
                                Grid::make()
                                    ->schema([
                                        TextInput::make('medida')
                                            ->label('Medida')
                                            ->numeric()
                                            ->nullable()
                                            ->columnSpan(['default' => 1, 'sm' => 1, 'md' => 1, 'lg' => 1, 'xl' => 1, '2xl' => 1]),
                                        Select::make('unidad_id')
                                            ->label('Unidad')
                                            ->relationship('unidad', 'nombre')
                                            ->searchable()
                                            ->placeholder('')
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('nombre')
                                                    ->label('Nombre de la unidad')
                                                    ->required()
                                                    ->unique('articulo_unidades', 'nombre'),
                                            ])
                                            ->columnSpan(['default' => 1, 'sm' => 1, 'md' => 1, 'lg' => 1, 'xl' => 1, '2xl' => 1]),
                                    ])
                                    ->columnSpan(['default' => 1, 'sm' => 1, 'md' => 1, 'lg' => 1, 'xl' => 1, '2xl' => 1])
                                    ->columns(['default' => 2, 'sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2]),
                                TextInput::make('color')
                                    ->label('Color')
                                    ->nullable(),
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
                                    ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, 'default' => 2]),
                                Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->columnSpanFull(),
                            ])
                            ->heading('Artículo')
                            ->columns(['default' => 1, 'sm' => 2, 'md' => 2, 'lg' => 2, 'xl' => 2, '2xl' => 2])
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 1, 'sm' => 1]),
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('grupo')
                                            ->relationship('grupo', 'nombre')
                                            ->searchable()
                                            ->preload(),
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
                                    ->heading('Ubicación en Almacén'),
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
                                            ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, 'default' => 2])
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
            ->searchOnBlur(true)
            ->columns([
                ColorColumn::make('grupo.color')
                    ->alignCenter()
                    ->placeholder('S.G.')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('categoria.nombre')
                    ->label('Artículo')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('marca.nombre')
                    ->placeholder('Sin marca')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('subCategoria.nombre')
                    ->placeholder('Sin grado o número')
                    ->label('Grado / Número')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('especificacion')
                    ->label('Especificación')
                    ->placeholder('Sin especificación')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('presentacion.nombre')
                    ->placeholder('Sin presentación')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('medida')
                    ->placeholder('0.00')
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('unidad.nombre')
                    ->placeholder('N/A')
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('color')
                    ->placeholder('Sin color')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('ubicaciones.codigo')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('costo')
                    ->label('Costo de compra')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(fn() => auth()->user()->can('update_articulo')),
                TextColumn::make('precio')
                    ->label('Precio de venta')
                    ->prefix('S/ ')
                    ->placeholder('S/ 0.00')
                    ->alignRight()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(fn() => auth()->user()->can('update_articulo')),
                TextColumn::make('stock')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return FractionService::decimalToFraction((float) $state);
                    }),
                TextColumn::make('abiertos')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return FractionService::decimalToFraction((float) $state);
                    }),
                TextColumn::make('mermas')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return FractionService::decimalToFraction((float) $state);
                    }),
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

                BulkAction::make('cambiar_grupo')
                    ->label('Cambiar Grupo')
                    ->color('gray')
                    ->form([
                        Select::make('grupo_id')
                            ->label('Grupo')
                            ->options(\App\Models\ArticuloGrupo::pluck('nombre', 'id'))
                            ->searchable()
                            ->required()
                            ->placeholder('Selecciona un grupo')
                    ])
                    ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records): void {
                        foreach ($records as $record) {
                            $record->update([
                                'grupo_id' => $data['grupo_id']
                            ]);
                        }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->modalWidth(MaxWidth::Large),
                ExportBulkAction::make(),
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ])
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession();
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
