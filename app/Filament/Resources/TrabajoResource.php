<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoResource\Pages;
use App\Filament\Resources\TrabajoResource\RelationManagers;
use App\Filament\Resources\TrabajoResource\RelationManagers\DescripcionTecnicosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\DescuentosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\DetallesRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\EvidenciasRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\InformesRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\OtrosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\PagosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\ServiciosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\TrabajoArticulosRelationManager;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\VehiculoMarca;
use App\Models\VehiculoModelo;
use App\Services\TrabajoService;
use Carbon\Carbon;
use DateTime;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
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
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class TrabajoResource extends Resource
{
    protected static ?string $model = Trabajo::class;

    protected static ?string $navigationGroup = 'Core';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Trabajo';

    protected static ?string $pluralModelLabel = 'Trabajos';

    protected static ?string $navigationLabel = 'Control vehicular';

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
                            ->disabled(fn() => auth()->user()->cannot('create_trabajo'))
                            ->columnSpan(1),
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Repeater::make('tecnicos')
                                            ->label('')
                                            ->createItemButtonLabel('Añadir técnico')
                                            ->relationship('tecnicos')
                                            ->defaultItems(0)
                                            ->simple(
                                                Select::make('tecnico_id')
                                                    ->label('Seleccionar Técnico')
                                                    ->relationship('tecnico', 'name', fn($query) => $query->withTrashed())
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                            )
                                    ])
                                    ->heading('Técnicos')
                                    ->hidden(
                                        fn(string $operation): bool =>
                                        $operation === 'edit' || auth()->user()->cannot('create_trabajo')
                                    ),
                                Section::make()
                                    ->schema([
                                        Toggle::make('disponible')
                                            ->label('Activar fuera de plazo')
                                            ->hiddenOn('create'),
                                        Repeater::make('tecnicos')
                                            ->label('')
                                            ->createItemButtonLabel('Añadir técnico')
                                            ->relationship('tecnicos')
                                            ->defaultItems(0)
                                            ->schema([
                                                Grid::make()
                                                    ->schema([
                                                        Select::make('tecnico_id')
                                                            ->label('Seleccionar Técnico')
                                                            ->relationship('tecnico', 'name', fn($query) => $query->withTrashed())
                                                            ->distinct()
                                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                            ->searchable()
                                                            ->preload()
                                                            ->required()
                                                            ->columnSpan(2),
                                                        Toggle::make('finalizado')
                                                            ->inline(false)
                                                            ->onIcon('heroicon-m-check')
                                                            ->offIcon('heroicon-m-x-mark')
                                                            ->onColor('success')
                                                            ->offColor('danger')
                                                            ->columnSpan(1)
                                                    ])
                                                    ->columns(3),
                                                // Grid::make()
                                                //     ->schema([
                                                //         Placeholder::make('created_at')
                                                //             ->label('Fecha de asignación')
                                                //             ->content(fn($record) => $record->created_at->format('d/m/Y H:i:s')),
                                                //         Placeholder::make('updated_at')
                                                //             ->label('Fecha de culminación')
                                                //             ->content(fn($record) => $record ? $record->updated_at->format('d/m/Y H:i:s') : 'No culminado')
                                                //     ])
                                                //     ->columns(2)
                                            ])
                                            ->collapsed()
                                            ->itemLabel(fn(array $state): ?string => $state['tecnico_id'] ? User::withTrashed()->find($state['tecnico_id'])->name : null)
                                    ])
                                    ->heading('Técnicos')
                                    ->hidden(
                                        fn(string $operation): bool =>
                                        $operation === 'create' || auth()->user()->cannot('create_trabajo')
                                    ),
                                // Section::make()
                                //     ->schema([
                                //         Repeater::make('archivos')
                                //             ->label('')
                                //             ->createItemButtonLabel('Añadir archivo')
                                //             ->defaultItems(0)
                                //             ->relationship()
                                //             ->simple(
                                //                 FileUpload::make('archivo_url')
                                //                     ->directory('trabajo_archivo')
                                //                     ->required()
                                //             )
                                //     ])
                                //     ->hidden(
                                //         fn(string $operation): bool =>
                                //         $operation === 'create' || auth()->user()->cannot('create_trabajo')
                                //     )
                                //     ->heading('Archivos'),
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
                CheckboxColumn::make('control')
                    ->alignCenter()
                    ->hidden(fn() => !auth()->user()->can('update_trabajo')),
                TextColumn::make('vehiculo.placa')
                    ->label('Placa')
                    ->placeholder('Sin Placa')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->color(function ($record) { // Accede al registro completo
                        return $record->control ? 'success' : 'gray'; // Color dinámico basado en "control"
                    }),
                TextColumn::make('vehiculo.tipoVehiculo.nombre')
                    ->label('Tipo')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
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
                TextColumn::make('clientes_display')
                    ->label('Clientes')
                    ->searchable(
                        isIndividual: true,
                        query: function (Builder $query, string $search): Builder {
                            return $query->whereHas('vehiculo.propietarios.cliente', function ($q) use ($search) {
                                $q->where('nombre', 'like', "%{$search}%");
                            });
                        }
                    )
                    ->badge()
                    ->wrap()
                    ->placeholder('Sin Clientes')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->getStateUsing(function ($record) {
                        $clientes = $record->vehiculo->propietarios->map(function ($propietario) {
                            $cliente = $propietario->cliente;

                            if (!empty($cliente->telefono)) {
                                $telefonoFormateado = self::formatearTelefono($cliente->telefono);
                                return "{$cliente->nombre} ({$telefonoFormateado})";
                            }

                            return $cliente->nombre;
                        });

                        return $clientes->isNotEmpty() ? $clientes->toArray() : null;
                    }),
                TextColumn::make('descripcion_servicio')
                    ->searchable(isIndividual: true)
                    ->wrap()
                    ->lineClamp(2)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('usuarios.name')
                    ->placeholder('Sin Técnicos')
                    ->label('Técnicos')
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: false),
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

                Action::make('terminar')
                    ->label('Terminar')
                    ->color('success')
                    ->icon('heroicon-s-check')
                    ->visible(fn(Trabajo $record) => is_null($record->fecha_salida)) // Visible solo si fecha_salida es null
                    ->action(function (Trabajo $record) {

                        $record->update([
                            'fecha_salida' => now()
                        ]);

                        TrabajoService::actualizarTrabajoPorId($record);

                        Notification::make()
                            ->title('El trabajo ha sido marcado como terminado.')
                            ->success()
                            ->send();
                    })
                    ->button()
                    ->size(ActionSize::Medium)
                    ->hidden(fn() => !auth()->user()->can('update_trabajo')),

                Action::make('reabrir')->requiresConfirmation()
                    ->modalHeading('Reabrir Trabajo')
                    ->modalDescription('¿Estás segura/o de que deseas reabrir el trabajo? Esto eliminará la fecha de salida actual, pero podrás asignar una nueva más tarde.')
                    ->label('Reabrir')
                    ->color('warning')
                    ->icon('heroicon-s-arrow-path')
                    ->visible(fn(Trabajo $record) => !is_null($record->fecha_salida)) // Visible solo si fecha_salida tiene valor
                    ->action(function (Trabajo $record) {
                        $record->update([
                            'fecha_salida' => null,
                            'desembolso' => null
                        ]);

                        Notification::make()
                            ->title('El trabajo ha sido reabierto.')
                            ->success()
                            ->send();
                    })
                    ->button()
                    ->size(ActionSize::Medium)
                    ->hidden(fn() => !auth()->user()->can('update_trabajo')),

                ActionGroup::make([
                    Action::make('Descargar informe')
                        ->icon('heroicon-s-document-text')
                        ->url(
                            fn(Trabajo $trabajo): string => route('trabajo.pdf.informe', ['trabajo' => $trabajo]),
                            shouldOpenInNewTab: true
                        )
                        ->hidden(fn() => !auth()->user()->can('view_trabajo::informe')),

                    Action::make('Descargar evidencias')
                        ->icon('heroicon-s-photo')
                        ->url(
                            fn(Trabajo $trabajo): string => route('trabajo.pdf.evidencia', ['trabajo' => $trabajo]),
                            shouldOpenInNewTab: true
                        )
                        ->hidden(fn() => !auth()->user()->can('view_evidencia')),
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
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('reiniciarControl')
                        ->label('Reiniciar Control')
                        ->requiresConfirmation()
                        ->modalHeading('Reiniciar Control')
                        ->modalDescription('¿Estás segura/o de que deseas reiniciar el control de todos los registros? Esta acción no se puede deshacer.')
                        ->action(function () {
                            DB::table('trabajos')->update(['control' => false]);
                            Notification::make()
                                ->title('Control reiniciado')
                                ->body('Todos los registros han sido actualizados correctamente.')
                                ->success()
                                ->send();
                        })
                        ->authorize(fn() => auth()->user()->can('create_trabajo'))
                        ->color('danger')
                        ->icon('heroicon-o-arrow-path'),

                    Action::make('cerrarAplazados')
                        ->label('Cerrar aplazados')
                        ->requiresConfirmation()
                        ->modalHeading('Cerrar trabajos aplazados')
                        ->modalDescription('¿Estás seguro/a de que deseas cerrar todos los trabajos marcados como aplazados?.')
                        ->action(function () {
                            DB::table('trabajos')->update(['disponible' => false]);
                            Notification::make()
                                ->title('Aplazados cerrados')
                                ->body('Todos los trabajos aplazados han sido actualizados correctamente.')
                                ->success()
                                ->send();
                        })
                        ->authorize(fn() => auth()->user()->can('create_trabajo'))
                        ->color('warning')
                        ->icon('heroicon-o-clock')
                ])
                    ->button()
                    ->color('gray'),
            ])
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->recordClasses(fn(Trabajo $record) => ($record->control && $record->fecha_salida !== null) ? 'desembolso-por-cobrar' : null);
    }

    public static function getRelations(): array
    {
        return [
            DescripcionTecnicosRelationManager::class,
            EvidenciasRelationManager::class,
            DetallesRelationManager::class,
            TrabajoArticulosRelationManager::class,
            InformesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrabajos::route('/'),
            'create' => Pages\CreateTrabajo::route('/create'),
            'view' => Pages\ViewTrabajo::route('/{record}'),
            'edit' => Pages\EditTrabajo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    private static function formatearTelefono($telefono)
    {
        if (empty($telefono)) {
            return $telefono;
        }

        // Limpiar el teléfono
        $telefonoLimpio = preg_replace('/[^0-9+]/', '', $telefono);

        // Extraer solo los números (sin el código de país)
        $numero = null;

        if (str_starts_with($telefonoLimpio, '+51') && strlen($telefonoLimpio) === 12) {
            $numero = substr($telefonoLimpio, 3);
        } elseif (str_starts_with($telefonoLimpio, '51') && strlen($telefonoLimpio) === 11) {
            $numero = substr($telefonoLimpio, 2);
        } elseif (str_starts_with($telefonoLimpio, '9') && strlen($telefonoLimpio) === 9) {
            $numero = $telefonoLimpio;
        }

        // Si encontramos un número peruano, formatearlo
        if ($numero && strlen($numero) === 9) {
            return '+51 ' . substr($numero, 0, 3) . ' ' . substr($numero, 3, 3) . ' ' . substr($numero, 6, 3);
        }

        // Para otros formatos, devolver el original
        return $telefono;
    }
}
