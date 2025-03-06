<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Filament\Resources\VentaResource\RelationManagers\VentaArticuloRelationManager;
use App\Models\Vehiculo;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationGroup = 'Core';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('codigo')
                                            ->default(now()->format('Ymdhis'))
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20)
                                            ->prefixIcon('heroicon-s-ticket'),
                                        TextInput::make('responsable')
                                            ->required()
                                            ->readOnly()
                                            ->prefixIcon('heroicon-s-user-circle')
                                            ->afterStateHydrated(function (TextInput $component, $context) {
                                                if ($context === 'create') {
                                                    $userName = Auth::user()->name;
                                                    $component->state($userName);
                                                }
                                            }),
                                        DatePicker::make('fecha')
                                            ->required()
                                            ->default(now()),
                                        TimePicker::make('hora')
                                            ->required()
                                            ->default(now()),
                                    ])->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                                Textarea::make('observacion')
                                    ->columnSpanFull(),
                            ])->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 3, 'sm' => 3]),
                        Section::make()
                            ->schema([
                                Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->prefixIcon('heroicon-s-user-circle')
                                    ->relationship('cliente', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('identificador')
                                            ->label('RUC / DNI')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(12),
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        PhoneInput::make('telefono')
                                            ->defaultCountry('PE')
                                            ->initialCountry('pe'),
                                    ])
                                    ->editOptionForm([
                                        TextInput::make('identificador')
                                            ->label('RUC / DNI')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(12),
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        PhoneInput::make('telefono')
                                            ->defaultCountry('PE')
                                            ->initialCountry('pe'),
                                    ]),

                                Select::make('vehiculo_id')
                                    ->label('Vehículo')
                                    ->prefixIcon('heroicon-s-truck')
                                    ->relationship('vehiculo', 'placa')
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $placa = $record->placa ?? '';
                                        $tipoVehiculo = $record->tipoVehiculo->nombre;
                                        $marca = $record->marca;
                                        $modelo = $record->modelo;
                                        return "{$placa} - {$tipoVehiculo} - {$marca} {$modelo}";
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        TextInput::make('placa')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(7)
                                            ->placeholder('ABC-123'),
                                        TextInput::make('marca')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('modelo')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('color')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->editOptionForm([
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        TextInput::make('placa')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(7),
                                        TextInput::make('marca')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('modelo')
                                            ->maxLength(255),
                                        TextInput::make('color')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ])->columnSpan(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                    ])
                    ->columns(['xl' => 5, 'lg' => 5, 'md' => 5, 'sm' => 5]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                ColumnGroup::make('Cliente', [
                    TextColumn::make('cliente.identificador')
                        ->label('RUC / DNI')
                        ->placeholder('Sin ID')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('cliente.nombre')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('cliente.telefono')
                        ->placeholder('Sin telefono')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Vehículo', [
                    TextColumn::make('vehiculo.placa')
                        ->label('Placa')
                        ->placeholder('Sin placa')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('vehiculo.tipoVehiculo.nombre')
                        ->label('Tipo')
                        ->placeholder('Sin placa')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('vehiculo.marca')
                        ->label('marca')
                        ->placeholder('Sin placa')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('vehiculo.modelo')
                        ->label('modelo')
                        ->placeholder('Sin placa')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                TextColumn::make('responsable.name')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('articulos_count')
                    ->label('Ítems')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('hora')
                    ->time('h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                DateRangeFilter::make('fecha'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Descargar')
                        ->icon('heroicon-s-arrow-down-tray')
                        ->form([
                            Checkbox::make('igv')
                                ->label('Incluir IGV')
                                ->reactive(),
                            TextInput::make('igv_porcentaje')
                                ->label('Porcentaje')
                                ->suffix('%')
                                ->default('18')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->disabled(function (callable $get) {
                                    return !$get('igv');
                                }),
                        ])
                        ->action(function (Venta $venta, array $data, $livewire) {

                            $params = [
                                'igv' => $data['igv'] ?? false,
                                'igv_porcentaje' => $data['igv_porcentaje'] ?? 18,
                            ];

                            $url = route('ventas.pdf', ['venta' => $venta] + $params);
                            $livewire->js("window.open('{$url}', '_blank');");
                        })
                        ->modalHeading('Configuración de Descarga')
                        ->modalButton('Descargar')
                        ->modalWidth('md'),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->button()
                    ->color('gray'),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('ventaArticulos as articulos_count');
    }

    public static function getRelations(): array
    {
        return [
            VentaArticuloRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
