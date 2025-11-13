<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotizacionResource\Pages;
use App\Filament\Resources\CotizacionResource\RelationManagers;
use App\Models\Cotizacion;
use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\VehiculoMarca;
use App\Models\VehiculoModelo;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class CotizacionResource extends Resource
{
    protected static ?string $model = Cotizacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Core';

    protected static ?string $pluralModelLabel = 'Cotizaciones';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Principal')
                    ->schema([
                        // ... (el resto del código de información principal se mantiene igual)
                        Forms\Components\Select::make('vehiculo_id')
                            ->label('Vehículo')
                            ->relationship('vehiculo')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $placa = $record->placa ?? '';
                                $tipoVehiculo = $record->tipoVehiculo->nombre ?? 'Sin tipo';
                                $marca = $record->marca?->nombre ?? 'Sin marca';
                                $modelo = $record->modelo?->nombre ?? 'Sin modelo';
                                $color = $record->color ?? '';

                                if (!empty($placa)) {
                                    return "{$placa} - {$tipoVehiculo} {$marca} {$modelo} {$color}";
                                } else {
                                    return "{$tipoVehiculo} {$marca} {$modelo} {$color}";
                                }
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\Select::make('tipo_vehiculo_id')
                                    ->relationship('tipoVehiculo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('placa')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('ABC-123'),

                                Forms\Components\Select::make('marca_id')
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

                                Forms\Components\Select::make('modelo_id')
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

                                Forms\Components\TextInput::make('color')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('vin')
                                    ->label('VIN / Chasis')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('motor')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ano')
                                    ->label('Año del modelo')
                                    ->maxLength(255),

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
                            ->editOptionForm([
                                Forms\Components\Select::make('tipo_vehiculo_id')
                                    ->relationship('tipoVehiculo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('placa')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('ABC-123'),

                                Forms\Components\Select::make('marca_id')
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

                                Forms\Components\Select::make('modelo_id')
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

                                Forms\Components\TextInput::make('color')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('vin')
                                    ->label('VIN / Chasis')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('motor')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ano')
                                    ->label('Año del modelo')
                                    ->maxLength(255),
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
                            ]),

                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->hintIcon('heroicon-m-information-circle')
                            ->hintIconTooltip('Este campo solo define el cliente que se mostrará en la proforma o presupuesto; no asocia un cliente al vehículo. Para eso, edita el vehículo más abajo. Si no se elige un cliente aquí, se usará por defecto el primero en la lista de los dueños.')
                            ->searchable()
                            ->searchPrompt('Buscar por nombre, RUC o DNI')
                            ->getSearchResultsUsing(function (string $search) {
                                return Cliente::query()
                                    ->where('nombre', 'like', "%{$search}%")
                                    ->orWhere('identificador', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($cliente) {
                                        $label = $cliente->identificador
                                            ? "{$cliente->identificador} - {$cliente->nombre}"
                                            : $cliente->nombre;
                                        return [$cliente->id => $label];
                                    });
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $cliente = Cliente::find($value);
                                return $cliente->identificador
                                    ? "{$cliente->identificador} - {$cliente->nombre}"
                                    : $cliente->nombre;
                            })
                            ->createOptionForm([
                                Forms\Components\TextInput::make('identificador')
                                    ->label('RUC / DNI')
                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                    ->maxLength(12),
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono')
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->maxLength(500),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return Cliente::create($data)->id;
                            })
                            ->nullable(),

                        Forms\Components\Toggle::make('igv')
                            ->label('Incluir IGV (18%)')
                            ->default(false)
                            ->live(),

                        Forms\Components\Textarea::make('observacion')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Servicios')
                    ->schema([
                        Repeater::make('servicios')
                            ->relationship('servicios')
                            ->schema([
                                Select::make('servicio_id')
                                    ->label('Servicio')
                                    ->relationship(
                                        name: 'servicio',
                                        titleAttribute: 'nombre',
                                        modifyQueryUsing: fn(Builder $query) => $query->with('tipoVehiculo')
                                    )
                                    ->getOptionLabelFromRecordUsing(function (Servicio $record) {
                                        return $record->tipoVehiculo
                                            ? "{$record->tipoVehiculo->nombre} - {$record->nombre}"
                                            : $record->nombre;
                                    })
                                    ->createOptionForm([
                                        Grid::make()
                                            ->schema([
                                                Select::make('tipo_vehiculo_id')
                                                    ->label('Tipo de Vehículo')
                                                    ->relationship('tipoVehiculo', 'nombre')
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                                TextInput::make('costo')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('S/ ')
                                                    ->maxValue(42949672.95)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                        Textarea::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ])
                                    ->editOptionForm([
                                        Grid::make()
                                            ->schema([
                                                Select::make('tipo_vehiculo_id')
                                                    ->label('Tipo de Vehículo')
                                                    ->relationship('tipoVehiculo', 'nombre')
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(1),
                                                TextInput::make('costo')
                                                    ->numeric()
                                                    ->required()
                                                    ->prefix('S/ ')
                                                    ->maxValue(42949672.95)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                        Textarea::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ])
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $servicio = Servicio::find($state);
                                        if ($servicio) {
                                            $set('precio', $servicio->costo);
                                        }
                                    })
                                    ->columnSpan(2),

                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->reactive(),

                                TextInput::make('precio')
                                    ->label('Precio unitario')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->reactive(),

                                Textarea::make('detalle')
                                    ->label('Detalles adicionales')
                                    ->nullable()
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->deleteAction(
                                fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation(),
                            )
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['servicio_id']) ?
                                (Servicio::find($state['servicio_id'])->nombre ?? 'Servicio no encontrado') :
                                'Nuevo servicio'
                            ),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Artículos')
                    ->schema([
                        Repeater::make('articulos')
                            ->relationship('articulos')
                            ->schema([
                                Forms\Components\TextInput::make('descripcion')
                                    ->label('Descripción del artículo')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required(),

                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio unitario')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->deleteAction(
                                fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation(),
                            )
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                $state['descripcion'] ?? 'Nuevo artículo'
                            ),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Resumen de la Cotización')
                    ->schema([
                        Forms\Components\Placeholder::make('subtotal_servicios')
                            ->label('Subtotal Servicios')
                            ->content(function (Get $get, Set $set) {
                                $servicios = $get('servicios') ?? [];
                                $subtotal = collect($servicios)->sum(function ($servicio) {
                                    return ($servicio['cantidad'] ?? 0) * ($servicio['precio'] ?? 0);
                                });
                                return 'S/ ' . number_format($subtotal, 2);
                            }),

                        Forms\Components\Placeholder::make('subtotal_articulos')
                            ->label('Subtotal Artículos')
                            ->content(function (Get $get) {
                                $articulos = $get('articulos') ?? [];
                                $subtotal = collect($articulos)->sum(function ($articulo) {
                                    return ($articulo['cantidad'] ?? 0) * ($articulo['precio'] ?? 0);
                                });
                                return 'S/ ' . number_format($subtotal, 2);
                            }),

                        Forms\Components\Placeholder::make('subtotal')
                            ->label('Subtotal General')
                            ->content(function (Get $get) {
                                $servicios = $get('servicios') ?? [];
                                $articulos = $get('articulos') ?? [];

                                $subtotalServicios = collect($servicios)->sum(function ($servicio) {
                                    return ($servicio['cantidad'] ?? 0) * ($servicio['precio'] ?? 0);
                                });

                                $subtotalArticulos = collect($articulos)->sum(function ($articulo) {
                                    return ($articulo['cantidad'] ?? 0) * ($articulo['precio'] ?? 0);
                                });

                                return 'S/ ' . number_format($subtotalServicios + $subtotalArticulos, 2);
                            }),

                        Forms\Components\Placeholder::make('igv_calculado')
                            ->label('IGV (18%)')
                            ->content(function (Get $get) {
                                $servicios = $get('servicios') ?? [];
                                $articulos = $get('articulos') ?? [];
                                $incluyeIgv = $get('igv') ?? false;

                                $subtotalServicios = collect($servicios)->sum(function ($servicio) {
                                    return ($servicio['cantidad'] ?? 0) * ($servicio['precio'] ?? 0);
                                });

                                $subtotalArticulos = collect($articulos)->sum(function ($articulo) {
                                    return ($articulo['cantidad'] ?? 0) * ($articulo['precio'] ?? 0);
                                });

                                $subtotal = $subtotalServicios + $subtotalArticulos;
                                $igv = $incluyeIgv ? $subtotal * 0.18 : 0;

                                return 'S/ ' . number_format($igv, 2);
                            }),

                        Forms\Components\Placeholder::make('total')
                            ->label('Total')
                            ->content(function (Get $get) {
                                $servicios = $get('servicios') ?? [];
                                $articulos = $get('articulos') ?? [];
                                $incluyeIgv = $get('igv') ?? false;

                                $subtotalServicios = collect($servicios)->sum(function ($servicio) {
                                    return ($servicio['cantidad'] ?? 0) * ($servicio['precio'] ?? 0);
                                });

                                $subtotalArticulos = collect($articulos)->sum(function ($articulo) {
                                    return ($articulo['cantidad'] ?? 0) * ($articulo['precio'] ?? 0);
                                });

                                $subtotal = $subtotalServicios + $subtotalArticulos;
                                $igv = $incluyeIgv ? $subtotal * 0.18 : 0;
                                $total = $subtotal + $igv;

                                return 'S/ ' . number_format($total, 2);
                            })
                            ->extraAttributes(['class' => 'font-bold text-lg']),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehiculo.placa')
                    ->label('Vehículo')
                    ->sortable()
                    ->searchable(isIndividual: true),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->placeholder('Sin cliente'),

                Tables\Columns\TextColumn::make('servicios_count')
                    ->label('Servicios')
                    ->counts('servicios')
                    ->sortable(),

                Tables\Columns\TextColumn::make('articulos_count')
                    ->label('Artículos')
                    ->counts('articulos')
                    ->sortable(),

                Tables\Columns\IconColumn::make('igv')
                    ->label('IGV')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->prefix('S/ ')
                    ->sortable()
                    ->getStateUsing(function (Cotizacion $record): float {
                        return $record->total;
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('con_igv')
                    ->label('Con IGV')
                    ->query(fn(Builder $query): Builder => $query->where('igv', true)),

                Tables\Filters\Filter::make('sin_igv')
                    ->label('Sin IGV')
                    ->query(fn(Builder $query): Builder => $query->where('igv', false)),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn(Cotizacion $record) => route('cotizaciones.pdf', $record))
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('clonar')
                        ->label('Clonar')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->action(function (Cotizacion $record) {
                            $nuevaCotizacion = self::clonarCotizacion($record);

                            Notification::make()
                                ->title('Cotización clonada exitosamente')
                                ->success()
                                ->send();

                            return redirect(CotizacionResource::getUrl('edit', ['record' => $nuevaCotizacion]));
                        }),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCotizaciones::route('/'),
            'create' => Pages\CreateCotizacion::route('/create'),
            'edit' => Pages\EditCotizacion::route('/{record}/edit'),
        ];
    }

    protected static function clonarCotizacion(Cotizacion $original): Cotizacion
    {
        return DB::transaction(function () use ($original) {
            // Clonar la cotización principal
            $nuevaCotizacion = new Cotizacion();
            $nuevaCotizacion->fill([
                'vehiculo_id' => $original->vehiculo_id,
                'cliente_id' => $original->cliente_id,
                'igv' => $original->igv,
                'observacion' => $original->observacion,
            ]);
            $nuevaCotizacion->save();

            // Clonar servicios (actualizado para la nueva estructura)
            foreach ($original->servicios as $servicio) {
                $nuevaCotizacion->servicios()->create([
                    'servicio_id' => $servicio->servicio_id,
                    'detalle' => $servicio->detalle,
                    'cantidad' => $servicio->cantidad,
                    'precio' => $servicio->precio,
                ]);
            }

            // Clonar artículos
            foreach ($original->articulos as $articulo) {
                $nuevaCotizacion->articulos()->create([
                    'descripcion' => $articulo->descripcion,
                    'cantidad' => $articulo->cantidad,
                    'precio' => $articulo->precio,
                ]);
            }

            // Recargar las relaciones para que estén disponibles
            $nuevaCotizacion->load(['servicios', 'articulos']);

            return $nuevaCotizacion;
        });
    }
}