<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContabilidadResource\Pages;
use App\Filament\Resources\ContabilidadResource\RelationManagers;
use App\Filament\Resources\ContabilidadResource\RelationManagers\DescuentosRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\DetallesRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\EvidenciasRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\InformesRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\OtrosRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\PagosRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\ServiciosRelationManager;
use App\Filament\Resources\ContabilidadResource\RelationManagers\TrabajoArticulosRelationManager;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Contabilidad;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Models\TrabajoPago;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\VehiculoMarca;
use App\Models\VehiculoModelo;
use App\Services\TrabajoService;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ContabilidadResource extends Resource
{
    protected static ?string $model = Contabilidad::class;

    protected static ?string $navigationGroup = 'Core';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'Control financiero';

    protected static ?string $pluralModelLabel = 'Control financiero';

    protected static ?string $navigationLabel = 'Control financiero';

    protected static ?string $slug = 'contabilidad';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('codigo')
                                    ->required()
                                    ->label('Código')
                                    ->placeholder('Ingrese una clave única de trabajo')
                                    ->unique(ignoreRecord: true)
                                    ->hiddenOn('create')
                                    ->prefixIcon('heroicon-s-key')
                                    ->maxLength(29),
                                Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->hintIcon('heroicon-m-information-circle') // 👈 iconito al lado del label
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
                                        TextInput::make('identificador')
                                            ->label('RUC / DNI')
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
                                    ->createOptionUsing(function (array $data) {
                                        return Cliente::create($data)->id;
                                    })
                                    ->hiddenOn('create'),
                                Select::make('vehiculo_id')
                                    ->relationship('vehiculo')
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $placa = $record->placa ?? '';
                                        $tipoVehiculo = $record->tipoVehiculo->nombre;
                                        $marca = $record->marca?->nombre;
                                        $modelo = $record->modelo?->nombre;
                                        $color = $record->color;
                                        if (!empty($placa)) {
                                            return "{$placa} {$tipoVehiculo} {$marca} {$modelo} {$color}";
                                        } else {
                                            return "{$tipoVehiculo} {$marca} {$modelo} {$color}";
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            // ->createOptionForm([
                                            //     TextInput::make('nombre')
                                            //         ->unique(ignoreRecord: true)
                                            //         ->required()
                                            //         ->maxLength(50),
                                            // ])
                                            // ->editOptionForm([
                                            //     TextInput::make('nombre')
                                            //         ->unique(ignoreRecord: true)
                                            //         ->required()
                                            //         ->maxLength(50),
                                            // ])
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
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            // ->createOptionForm([
                                            //     TextInput::make('nombre')
                                            //         ->unique(ignoreRecord: true)
                                            //         ->required()
                                            //         ->maxLength(50),
                                            // ])
                                            // ->editOptionForm([
                                            //     TextInput::make('nombre')
                                            //         ->unique(ignoreRecord: true)
                                            //         ->required()
                                            //         ->maxLength(50),
                                            // ])
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
                                Select::make('conductor_id')
                                    ->hiddenOn('create')
                                    ->label('Conductor')
                                    ->relationship('conductor', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(
                                        fn(Cliente $record) =>
                                        $record->identificador
                                        ? "{$record->identificador} - {$record->nombre}"
                                        : $record->nombre
                                    )
                                    ->createOptionForm([
                                        TextInput::make('identificador')
                                            ->label('RUC / DNI')
                                            ->unique(table: 'clientes', ignoreRecord: true)
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
                                    ->editOptionForm([
                                        TextInput::make('identificador')
                                            ->label('RUC / DNI')
                                            ->unique(table: 'clientes', ignoreRecord: true)
                                            ->maxLength(12),
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        PhoneInput::make('telefono')
                                            ->defaultCountry('PE')
                                            ->initialCountry('pe'),
                                        TextInput::make('direccion')
                                            ->label('Dirección')
                                    ]),
                                Select::make('taller_id')
                                    ->relationship('taller', 'nombre')
                                    ->default(1)
                                    ->required(),
                                // ->createOptionForm([
                                //     TextInput::make('nombre')
                                //         ->unique(ignoreRecord: true)
                                //         ->required()
                                //         ->maxLength(255),
                                //     TextInput::make('ubicacion')
                                //         ->required()
                                //         ->maxLength(255),
                                // ])
                                // ->editOptionForm([
                                //     TextInput::make('nombre')
                                //         ->unique(ignoreRecord: true)
                                //         ->required()
                                //         ->maxLength(255),
                                //     TextInput::make('ubicacion')
                                //         ->required()
                                //         ->maxLength(255),
                                // ]),
                                DateTimePicker::make('fecha_ingreso')
                                    ->default(now())
                                    ->required(),
                                DateTimePicker::make('fecha_salida')
                                    ->hiddenOn('create'),
                                TextInput::make('kilometraje')
                                    ->numeric()
                                    ->maxValue(42949672.95),
                                Textarea::make('descripcion_servicio')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->heading('Trabajo')
                            ->columnSpan(1),
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('garantia')
                                            ->label('Garantía'),
                                        RichEditor::make('observaciones')
                                            ->label('Observaciones')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'heading',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'table',
                                                'undo',
                                            ]),
                                    ])
                                    ->heading('Detalles'),
                                Section::make()
                                    ->schema([
                                        Repeater::make('comprobantes')
                                            ->collapsed()
                                            ->itemLabel(
                                                fn(array $state): ?string =>
                                                Comprobante::find($state['comprobante_id'])?->codigo
                                            )
                                            ->label('Comprobantes')
                                            ->schema([
                                                Select::make('comprobante_id')
                                                    ->label('Comprobante')
                                                    ->options(fn() => Comprobante::query()->pluck('codigo', 'id'))
                                                    ->searchable()
                                                    ->required()
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->createOptionForm([
                                                        Grid::make()
                                                            ->schema([
                                                                TextInput::make('codigo')
                                                                    ->required()
                                                                    ->unique('comprobantes', 'codigo')
                                                                    ->validationMessages([
                                                                        'unique' => 'Este código de comprobante ya existe',
                                                                    ]),
                                                                DatePicker::make('emision')
                                                                    ->default(now())
                                                                    ->required(),
                                                                TextInput::make('total')
                                                                    ->numeric()
                                                                    ->required(),
                                                            ]),
                                                        FileUpload::make('url')
                                                            ->required()
                                                            ->directory('comprobantes'),
                                                    ])
                                                    ->createOptionUsing(function (array $data) {
                                                        return DB::transaction(function () use ($data) {
                                                            $comprobante = Comprobante::create([
                                                                'codigo' => $data['codigo'],
                                                                'emision' => $data['emision'],
                                                                'total' => $data['total'],
                                                                'url' => $data['url'],
                                                            ]);
                                                            return $comprobante->id;
                                                        });
                                                    })
                                                    ->editOptionForm(function ($state) {
                                                        $comprobante = Comprobante::find($state);

                                                        return [
                                                            Grid::make()
                                                                ->schema([
                                                                    Hidden::make('id')->default($comprobante->id), // Añadido para mantener referencia
                                                                    TextInput::make('codigo')
                                                                        ->required()
                                                                        ->rules([
                                                                            Rule::unique('comprobantes', 'codigo')
                                                                                ->ignore($comprobante->id)
                                                                        ])
                                                                        ->validationMessages([
                                                                            'unique' => 'Este código de comprobante ya existe',
                                                                        ]),
                                                                    DatePicker::make('emision')
                                                                        ->required(),
                                                                    TextInput::make('total')
                                                                        ->numeric()
                                                                        ->required(),
                                                                ]),
                                                            FileUpload::make('url')
                                                                ->directory('comprobantes'),
                                                        ];
                                                    })
                                                    ->fillEditOptionActionFormUsing(function (string $state) {
                                                        if ($state) {
                                                            $comprobante = Comprobante::find($state);
                                                            return $comprobante ? ['comprobante_id' => $comprobante->id] + $comprobante->toArray() : [];
                                                        }
                                                        return [];
                                                    })
                                                    ->updateOptionUsing(function (array $data, string $state) {
                                                        DB::transaction(function () use ($data, $state) {
                                                            $comprobante = Comprobante::findOrFail($state);
                                                            $comprobante->update([
                                                                'codigo' => $data['codigo'],
                                                                'emision' => $data['emision'],
                                                                'total' => $data['total'],
                                                                'url' => $data['url'] ?? $comprobante->url,
                                                            ]);
                                                        });
                                                    }),
                                            ])
                                            ->defaultItems(0)
                                            ->minItems(0)
                                            ->reorderable(false)
                                            ->columnSpanFull()
                                            ->addActionLabel('Agregar Comprobante')
                                            ->afterStateHydrated(function (Set $set, ?Contabilidad $record) {
                                                if (!$record || !$record->exists) {
                                                    return;
                                                }
                                                $items = $record->comprobantes()
                                                    ->pluck('comprobantes.id')
                                                    ->map(fn($id) => ['comprobante_id' => $id])
                                                    ->values()
                                                    ->toArray();

                                                $set('comprobantes', $items);
                                            }),
                                    ])
                                    ->heading('Comprobantes'),
                            ])
                            ->columnspan(1)
                            ->columns(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fecha_ingreso')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('hora_ingreso')
                    ->getStateUsing(function ($record) {
                        return $record->fecha_ingreso->format('h:i:s A');
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vehiculo.placa')
                    ->label('Placa')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Sin Placa')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('vehiculo.tipoVehiculo.nombre')
                    ->label('Tipo')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vehiculo.marca.nombre')
                    ->placeholder('Sin Marca')
                    ->label('Marca')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('vehiculo.modelo.nombre')
                    ->placeholder('Sin Modelo')
                    ->label('Modelo')
                    ->wrap()
                    ->lineClamp(2)
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vehiculo.color')
                    ->label('Color')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vehiculo.clientes.nombre')
                    ->placeholder('Sin Clientes')
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('descripcion_servicio')
                    ->searchable(isIndividual: true)
                    ->wrap()
                    ->lineClamp(2)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('usuarios.name')
                    ->placeholder('Sin Técnicos')
                    ->label('Técnicos')
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fecha_salida')
                    ->label('Fecha salida')
                    ->state(function ($record) {
                        return $record->fecha_salida
                            ? Carbon::parse($record->fecha_salida)->format('d/m/Y')
                            : $record->taller->nombre;
                    }),
                TextColumn::make('hora_salida')
                    ->placeholder('Sin Salida')
                    ->getStateUsing(function ($record) {
                        if ($record->fecha_salida) {
                            return $record->fecha_salida->format('h:i:s A');
                        }
                        return '';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                SelectColumn::make('desembolso')
                    ->placeholder('')
                    ->options([
                        'A CUENTA' => 'A CUENTA',
                        'COBRADO' => 'COBRADO',
                        'POR COBRAR' => 'POR COBRAR',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false),
                ToggleColumn::make('presupuesto_enviado')
                    ->alignCenter()
                    ->label('Presupuesto')
                    ->onIcon('heroicon-s-envelope')
                    ->offIcon('heroicon-s-envelope-open')
                    ->onColor('success')
                    ->toggleable(true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('importe')
                    ->sortable()
                    ->alignRight()
                    ->label('Importe total')
                    ->prefix('S/ ')
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago'))
                    ->toggleable(isToggledHiddenByDefault: false),
                // TextColumn::make('importe_2')
                //     ->getStateUsing(fn($record) => $record->importe())
                //     ->alignRight()
                //     ->prefix('S/ ')
                //     ->formatStateUsing(fn($state): string => number_format($state, 2, '.', ','))
                //     ->hidden(fn() => !auth()->user()->can('view_trabajo::pago')),
                TextColumn::make('a_cuenta')
                    ->sortable()
                    ->alignRight()
                    ->prefix('S/ ')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('.getPorCobrar')
                    ->alignRight()
                    ->label('Por cobrar')
                    ->getStateUsing(function (Trabajo $record): string {
                        return number_format($record->getPorCobrar(), 2, '.', '');
                    })
                    ->prefix('S/ ')
                    ->toggleable(isToggledHiddenByDefault: false),

                
                ToggleColumn::make('aplica_detraccion')
                    ->alignCenter()
                    ->label('Aplica detracción')
                    ->onIcon('heroicon-s-currency-dollar')
                    ->offIcon('heroicon-s-banknotes')
                    ->onColor('warning')
                    ->toggleable(true)
                    ->toggleable(isToggledHiddenByDefault: false),
TextColumn::make('importe_neto')
    ->label('Importe neto')
    ->alignRight()
    ->prefix('S/ ')
    ->getStateUsing(function ($record) {
        $importe = $record->importe;
        // Si aplica detracción, calcula el 88%, sino toma el 100%
        $importeNeto = $record->aplica_detraccion ? $importe * 0.88 : $importe;
        return number_format($importeNeto, 2, '.', '');
    })
    ->toggleable(isToggledHiddenByDefault: false),
    
                Tables\Columns\ViewColumn::make('comprobantes_badges')
                    ->label('Comprobantes')
                    ->disableClick()
                    ->view('filament.tables.columns.comprobantes-badges')
                    ->state(function ($record) {

                        return $record->comprobantes->map(function ($c) {
                            $path = $c->url;
                            $href = $path
                                ? (Str::startsWith($path, ['http://', 'https://', '/'])
                                    ? $path
                                    : Storage::disk('public')->url($path))
                                : null;

                            return [
                                'id' => $c->id,
                                'codigo' => $c->codigo,
                                'emision' => $c->emision?->toDateTimeString(), // Convertir a string si es Carbon
                                'total' => (float) $c->total, // Asegurar que sea float
                                'url' => $href,
                            ];
                        })->values()->all();
                    }),

                Tables\Columns\ViewColumn::make('pagos_badges')
                    ->label('Pagos')
                    ->disableClick()
                    ->view('filament.tables.columns.pagos-badges')
                    ->state(function (Trabajo $record) {
                        return $record->pagos
                            ->map(function (TrabajoPago $pago) {
                                return [
                                    'id' => $pago->id,
                                    'monto' => number_format((float) $pago->monto, 2, '.', ''),
                                    'fecha_pago' => optional($pago->fecha_pago)->format('d/m/Y'),
                                    'detalle' => $pago->detalle?->nombre,
                                    'observacion' => $pago->observacion,
                                ];
                            })
                            ->values()
                            ->all();
                    }),

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
                TextColumn::make('deleted_at')
                    ->label('Fecha de eliminación')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_ingreso', 'desc')
            ->recordUrl(function (Trabajo $record): ?string {
                if (auth()->user()->can('update_trabajo')) {
                    return static::getUrl('edit', ['record' => $record]);
                } elseif (auth()->user()->can('view_trabajo')) {
                    return static::getUrl('view', ['record' => $record]);
                }
                return null;
            })
            ->striped()
            ->filters([
                Filter::make('control_o_fecha_salida_null')
                    ->label('Control vehicular')
                    ->query(fn($query) => $query->where('control', true)->orWhereNull('fecha_salida'))
                    ->hidden(fn() => !auth()->user()->can('update_trabajo')),
                TernaryFilter::make('estado_trabajo')
                    ->label('Estado del Trabajo')
                    ->placeholder('Todos')
                    ->trueLabel('En taller')
                    ->falseLabel('Finalizados')
                    // ->default(true)
                    ->queries(
                        true: fn($query) => $query->whereNull('fecha_salida'),
                        false: fn($query) => $query->whereNotNull('fecha_salida'),
                        blank: fn($query) => $query, // Mostrar todos sin filtrar
                    ),
                SelectFilter::make('taller_id')
                    ->label('Taller')
                    ->relationship('taller', 'nombre'),
                SelectFilter::make('desembolso')
                    ->label('Estado del Desembolso')
                    ->options([
                        'A CUENTA' => 'A Cuenta',
                        'COBRADO' => 'Cobrado',
                        'POR COBRAR' => 'Por Cobrar',
                    ])
                    ->placeholder('Todos')
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago')),
                TernaryFilter::make('presupuestos')
                    ->label('Estado de presupuesto')
                    ->placeholder('Todos')
                    ->trueLabel('Enviados')
                    ->falseLabel('Por enviar')
                    ->queries(
                        true: fn($query) => $query->where('presupuesto_enviado', true),
                        false: fn($query) => $query->where('presupuesto_enviado', false),
                        blank: fn($query) => $query, // Mostrar todos (opción por defecto)
                    ),
                DateRangeFilter::make('fecha_ingreso'),
                DateRangeFilter::make('fecha_salida'),
                TernaryFilter::make('aplazados')
                    ->label('Trabajos aplazados')
                    ->placeholder('Todos')
                    ->trueLabel('Solo aplazados')
                    ->falseLabel('Excluir aplazados')
                    ->queries(
                        true: fn($query) => $query->where('disponible', true),
                        false: fn($query) => $query->where('disponible', false),
                        blank: fn($query) => $query, // Mostrar todos (opción por defecto)
                    ),
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([

                    Action::make('Descargar presupuesto')
                        ->icon('heroicon-s-document-currency-dollar')
                        ->form([
                            // Grid::make()
                            //     ->schema([
                            //         Section::make()
                            //             ->schema([
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
                            // ])
                            // ->heading('Configuración de IGV')
                            // ->columnSpan(['xl' => 1, 'lg' => 1, 'md' => 1, 'sm' => 1]),

                            // Section::make()
                            //     ->schema([
                            //         Checkbox::make('servicios')
                            //             ->label('Incluir servicios')
                            //             ->default(true),
                            //         Checkbox::make('articulos')
                            //             ->label('Incluir artículos')
                            //             ->default(true),
                            //     ])
                            //     ->heading('Opciones de Descarga')
                            //     ->columnSpan(['xl' => 1, 'lg' => 1, 'md' => 1, 'sm' => 1]),
                            // ])
                            // ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                        ])
                        ->action(function (Contabilidad $trabajo, array $data, $livewire) {

                            $params = [
                                'igv' => $data['igv'] ?? false,
                                'igv_porcentaje' => $data['igv_porcentaje'] ?? 18,
                                // 'servicios' => $data['servicios'] ?? true,
                                // 'articulos' => $data['articulos'] ?? true,
                            ];

                            $url = route('trabajo.pdf.presupuesto', ['trabajo' => $trabajo] + $params);
                            $livewire->js("window.open('{$url}', '_blank');");
                        })
                        ->modalHeading('Configuración de Descarga')
                        ->modalButton('Descargar')
                        ->modalWidth('md')
                        ->hidden(fn() => !auth()->user()->can('view_trabajo::pago')),

                    Action::make('Descargar proforma')
                        ->icon('heroicon-s-document')
                        ->url(
                            fn(Contabilidad $trabajo): string => route('trabajo.pdf.proforma', ['trabajo' => $trabajo]),
                            shouldOpenInNewTab: true
                        ),
                ])
                    ->button()
                    ->label('Descargar')
                    ->icon('heroicon-s-arrow-down-tray'),

                ActionGroup::make([

                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
                    ->color('gray')
                    ->button(),

                Action::make('verPago')
                    ->label('Ver pago')
                    ->icon('heroicon-o-currency-dollar')
                    ->extraAttributes(['class' => 'hidden']) // oculta en UI, pero registrada
                    ->modalHeading('Detalles del pago')
                    ->modalWidth(MaxWidth::Large)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->mountUsing(function (Form $form, Trabajo $record, array $arguments) {
                        $pagoId = $arguments['pago_id'] ?? null;
                        if (!$pagoId)
                            return;

                        $pago = TrabajoPago::with('detalle')->findOrFail($pagoId);

                        $form->fill([
                            // estado “oculto” que leerán los Placeholder
                            'monto_state' => number_format((float) $pago->monto, 2, '.', ''),
                            'fecha_pago_state' => $pago->fecha_pago ? Carbon::parse($pago->fecha_pago)->format('d/m/Y') : '—',
                            'detalle_state' => $pago->detalle?->nombre ?? '—',
                            'observacion_state' => $pago->observacion ?? '—',
                        ]);
                    })
                    ->form([
                        Grid::make(2)->schema([
                            Placeholder::make('monto_view')
                                ->label('Monto')
                                ->content(fn(Get $get) => 'S/ ' . number_format((float) ($get('monto_state') ?? 0), 2, '.', '')),

                            Placeholder::make('fecha_pago_view')
                                ->label('Fecha de pago')
                                ->content(fn(Get $get) => $get('fecha_pago_state') ?: '—'),

                            Placeholder::make('detalle_view')
                                ->label('Detalle')
                                ->content(fn(Get $get) => $get('detalle_state') ?: '—')
                                ->columnSpan(2),

                            Placeholder::make('observacion_view')
                                ->label('Observación')
                                ->content(fn(Get $get) => $get('observacion_state') ?: '—')
                                ->columnSpan(2),

                            // registran las keys de estado que llenamos en mountUsing()
                            Hidden::make('monto_state'),
                            Hidden::make('fecha_pago_state'),
                            Hidden::make('detalle_state'),
                            Hidden::make('observacion_state'),
                        ]),
                    ])
                    ->closeModalByClickingAway(true)
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ])
            ->headerActions([])
            ->recordClasses(fn(Trabajo $record) => match ($record->desembolso) {
                'A CUENTA' => 'desembolso-a-cuenta',
                'COBRADO' => 'desembolso-cobrado',
                'POR COBRAR' => 'desembolso-por-cobrar',
                default => null,
            });
    }

    public static function getRelations(): array
    {
        return [
            ServiciosRelationManager::class,
            TrabajoArticulosRelationManager::class,
            OtrosRelationManager::class,
            PagosRelationManager::class,
            DescuentosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContabilidades::route('/'),
            // 'create' => Pages\CreateContabilidad::route('/create'),
            'view' => Pages\ViewContabilidad::route('/{record}'),
            'edit' => Pages\EditContabilidad::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with([
                'pagos:id,trabajo_id,monto,fecha_pago,detalle_id,observacion',
                'pagos.detalle:id,nombre',
                'comprobantes:id,total,emision,codigo,url', // Añade esta línea
            ]);
    }
}
