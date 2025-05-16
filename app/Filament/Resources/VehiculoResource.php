<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculoResource\Pages;
use App\Filament\Resources\VehiculoResource\RelationManagers;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\VehiculoMarca;
use App\Models\VehiculoModelo;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    protected static ?string $navigationGroup = 'Core';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $modelLabel = 'Vehículo';

    protected static ?string $pluralModelLabel = 'Vehículos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('tipo_vehiculo_id')
                                    ->label('Tipo de vehículo')
                                    ->relationship('tipoVehiculo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(50),
                                    ])
                                    ->editOptionForm([
                                        TextInput::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(50),
                                    ])
                                    ->required(),
                                TextInput::make('placa')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('ABC-123'),

                                Select::make('marca_id')
                                    ->label('Marca')
                                    ->relationship('marca', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre de la Marca')
                                            ->required()
                                            ->unique(VehiculoMarca::class, 'nombre'),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return VehiculoMarca::create($data)->getKey();
                                    })
                                    ->editOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre de la Marca')
                                            ->required()
                                            ->unique(VehiculoMarca::class, 'nombre', ignoreRecord: true),
                                    ])
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('modelo_id', null);
                                    })
                                    ->reactive()
                                    ->nullable(),

                                Select::make('modelo_id')
                                    ->label('Modelo')
                                    ->relationship('modelo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn(Get $get) => blank($get('marca_id')))
                                    ->options(fn(Get $get) => VehiculoModelo::query()
                                        ->when($get('marca_id'), fn($query, $marcaId) => $query->where('marca_id', $marcaId))
                                        ->pluck('nombre', 'id'))
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre del Modelo')
                                            ->required()
                                            ->unique(VehiculoModelo::class, 'nombre'),
                                    ])
                                    ->createOptionUsing(function (array $data, Get $get) {
                                        return VehiculoModelo::create([
                                            'nombre' => $data['nombre'],
                                            'marca_id' => $get('marca_id'),
                                        ])->getKey();
                                    })
                                    ->editOptionForm([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre del Modelo')
                                            ->required()
                                            ->unique(VehiculoModelo::class, 'nombre', ignoreRecord: true),
                                    ])
                                    ->reactive()
                                    ->nullable(),

                                TextInput::make('color')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('vin')
                                    ->label('VIN / Chasis')
                                    ->maxLength(255),
                                TextInput::make('motor')
                                    ->maxLength(255),
                                TextInput::make('ano')
                                    ->label('Año del modelo')
                                    ->maxLength(255),
                            ])
                            ->heading('Vehículo')
                            ->columnSpan(1),
                        Section::make()
                            ->schema([
                                Repeater::make('propietarios')
                                    ->relationship()
                                    ->simple(
                                        Select::make('cliente_id')
                                            ->label('Seleccionar Cliente')
                                            ->relationship('cliente', 'nombre_completo', fn($query) => $query->withTrashed())
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('identificador')
                                                    ->label('RUC / DNI')
                                                    // ->required()
                                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                                    ->maxLength(12),
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                PhoneInput::make('telefono')
                                                    ->defaultCountry('PE')
                                                    ->initialCountry('pe'),
                                                TextInput::make('direccion')
                                                    ->label('Dirección')
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                return Cliente::create($data)->getKey();
                                            })
                                            ->editOptionForm([
                                                TextInput::make('identificador')
                                                    ->label('RUC / DNI')
                                                    // ->required()
                                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                                    ->maxLength(12),
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                PhoneInput::make('telefono')
                                                    ->defaultCountry('PE')
                                                    ->initialCountry('pe'),
                                                TextInput::make('direccion')
                                                    ->label('Dirección')
                                            ])
                                            ->getOptionLabelUsing(function ($value): ?string {
                                                $cliente = Cliente::withTrashed()->find($value);
                                                return $cliente ? $cliente->nombre_completo : 'Cliente eliminado';
                                            })
                                            ->required()
                                    )
                                    ->defaultItems(0)
                            ])
                            ->heading('Clientes')
                            ->columnSpan(1)
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->columns([
                TextColumn::make('placa')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->placeholder('Sin Placa'),
                TextColumn::make('tipoVehiculo.nombre')
                    ->label('Tipo')
                    ->numeric()
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('marca.nombre')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('modelo.nombre')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('color')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('clientes.nombre')
                    ->placeholder('Sin Clientes')
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->wrap(),
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
            'index' => Pages\ListVehiculos::route('/'),
            'create' => Pages\CreateVehiculo::route('/create'),
            'edit' => Pages\EditVehiculo::route('/{record}/edit'),
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
