<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages;
use App\Filament\Resources\ArticuloResource\RelationManagers;
use App\Models\Almacen;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\SubCategoria;
use App\Models\Ubicacion;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                                    ->afterStateUpdated(fn($state, callable $set) => $set('sub_categoria_id', null)),
                                Select::make('sub_categoria_id')
                                    ->required()
                                    ->label('Subcategoría')
                                    ->placeholder('')
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
                                    ->disabled(fn($get) => !$get('categoria_id')),
                                TextInput::make('marca')
                                    ->required(),
                                TextInput::make('tamano_presentacion')
                                    ->label('Tamaño / Presentación')
                                    ->required(),
                                TextInput::make('precio')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95),
                                Textarea::make('descripcion')
                                    ->label('Descripción'),
                            ])
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 1, 'sm' => 1]),
                        Section::make()
                            ->schema([
                                Repeater::make('ubicaciones')
                                    ->relationship('ubicaciones')
                                    // ->defaultItems(0)
                                    ->schema([
                                        Select::make('almacen_id')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->label('Almacén')
                                            ->options(Almacen::all()->pluck('nombre', 'id'))
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, callable $set) => $set('ubicacion_id', null)),
                                        Select::make('ubicacion_id')
                                            ->required()
                                            ->label('Ubicación')
                                            ->placeholder('')
                                            ->searchable()
                                            ->options(function ($get) {
                                                $almacenId = $get('almacen_id');
                                                if ($almacenId) {
                                                    return Ubicacion::where('almacen_id', $almacenId)
                                                        ->pluck('nombre', 'id')
                                                        ->toArray();
                                                }
                                                return [];
                                            })
                                            ->disabled(fn($get) => !$get('almacen_id')),
                                    ])
                                    // ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                    //     $data['almacen_id'] = '2';
                                    //     $data['ubicacion_id'] = '5';
                                    //     // dd($data);
                                    //     return $data;
                                    // })
                                    // ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                    //     // $data['ubicaciones'] = [
                                    //     //     [
                                    //     //         'almacen_id' => 1,
                                    //     //         'ubicacion_id' => 10,
                                    //     //     ],
                                    //     //     [
                                    //     //         'almacen_id' => 2,
                                    //     //         'ubicacion_id' => 20,
                                    //     //     ],
                                    //     // ];
                                    //     // dd($data);
                                    //     return $data;
                                    // }),
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
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('subCategoria.nombre')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('marca')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tamano_presentacion')
                    ->label('Tamaño')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('precio')
                    ->prefix('S/ '),
                TextColumn::make('ubicacion.almacen.nombre')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('ubicacion.nombre')
                    ->sortable()
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
            ->filters([
                ValueRangeFilter::make('precio')
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
}
