<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotizacionResource\Pages;
use App\Filament\Resources\CotizacionResource\RelationManagers;
use App\Models\Cotizacion;
use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\VehiculoMarca;
use App\Models\VehiculoModelo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;

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
                                Forms\Components\TextInput::make('descripcion')
                                    ->label('Descripción del servicio')
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
                                $state['descripcion'] ?? 'Nuevo servicio'
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
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehiculo.placa')
                    ->label('Vehículo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
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
                    ->money('PEN')
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
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn(Cotizacion $record) => route('cotizaciones.pdf', $record))
                    ->openUrlInNewTab(),
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

            // Clonar servicios
            foreach ($original->servicios as $servicio) {
                $nuevaCotizacion->servicios()->create([
                    'descripcion' => $servicio->descripcion,
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