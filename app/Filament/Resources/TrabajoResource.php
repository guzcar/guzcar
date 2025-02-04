<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoResource\Pages;
use App\Filament\Resources\TrabajoResource\RelationManagers;
use App\Filament\Resources\TrabajoResource\RelationManagers\EvidenciasRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\PagosRelationManager;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class TrabajoResource extends Resource
{
    protected static ?string $model = Trabajo::class;

    protected static ?string $navigationGroup = 'Core';

    protected static ?int $navigationSort = -1;

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
                                Select::make('vehiculo_id')
                                    ->relationship('vehiculo', 'nombre_completo')
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm([
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
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
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
                                                            ->required()
                                                            ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                                            ->maxLength(12),
                                                        TextInput::make('nombre')
                                                            ->required()
                                                            ->maxLength(255),
                                                        PhoneInput::make('telefono')
                                                    ])
                                                    ->createOptionUsing(function (array $data): int {
                                                        return Cliente::create($data)->getKey();
                                                    })
                                                    ->editOptionForm([
                                                        TextInput::make('identificador')
                                                            ->label('RUC / DNI')
                                                            ->required()
                                                            ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                                            ->maxLength(12),
                                                        TextInput::make('nombre')
                                                            ->required()
                                                            ->maxLength(255),
                                                        PhoneInput::make('telefono')
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
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
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
                                                    ])
                                                    ->getOptionLabelUsing(function ($value): ?string {
                                                        $cliente = Cliente::withTrashed()->find($value);
                                                        return $cliente ? $cliente->nombre_completo : 'Cliente eliminado';
                                                    })
                                                    ->required()
                                            )
                                            ->defaultItems(0)
                                    ]),
                                Select::make('taller_id')
                                    ->relationship('taller', 'nombre')
                                    ->preload()
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
                                DatePicker::make('fecha_ingreso')
                                    ->default(now())
                                    ->required(),
                                DatePicker::make('fecha_salida')
                                    ->hiddenOn('create'),
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
                                        Repeater::make('mecanicos')
                                            ->relationship('mecanicos') // Relación hacia TrabajoMecanico en el modelo Trabajo
                                            ->defaultItems(0)
                                            ->simple(
                                                Select::make('mecanico_id') // Campo mecanico_id
                                                    ->label('Seleccionar Mecánico')
                                                    ->relationship('mecanico', 'name', fn($query) => $query->withTrashed()) // Relación hacia User desde TrabajoMecanico
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->searchable()
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->label('Nombre')
                                                            ->required()
                                                            ->maxLength(255),
                                                        TextInput::make('email')
                                                            ->label('Correo electrónico')
                                                            ->unique(ignoreRecord: true)
                                                            ->email()
                                                            ->required()
                                                            ->maxLength(255),
                                                        TextInput::make('password')
                                                            ->label('Contraseña')
                                                            ->password()
                                                            ->confirmed()
                                                            ->dehydrated(fn($state) => filled($state))
                                                            ->required()
                                                            ->minLength(8),
                                                        TextInput::make('password_confirmation')
                                                            ->label('Confirmar contraseña')
                                                            ->password()
                                                            ->dehydrated(fn($state) => filled($state))
                                                            ->required()
                                                            ->minLength(8),
                                                    ])
                                                    ->createOptionUsing(function (array $data): int {
                                                        $data['password'] = bcrypt($data['password']);
                                                        return User::create($data)->getKey();
                                                    })
                                                    ->editOptionForm([
                                                        TextInput::make('name')
                                                            ->label('Nombre')
                                                            ->required()
                                                            ->maxLength(255),
                                                        TextInput::make('email')
                                                            ->label('Correo electrónico')
                                                            ->unique(ignoreRecord: true)
                                                            ->email()
                                                            ->required()
                                                            ->maxLength(255),
                                                        TextInput::make('password')
                                                            ->label('Contraseña')
                                                            ->password()
                                                            ->confirmed()
                                                            ->dehydrated(fn($state) => filled($state))
                                                            ->minLength(8),
                                                        TextInput::make('password_confirmation')
                                                            ->label('Confirmar contraseña')
                                                            ->password()
                                                            ->dehydrated(fn($state) => filled($state))
                                                            ->minLength(8),
                                                    ])
                                                    ->getOptionLabelUsing(function ($value): ?string {
                                                        $user = User::withTrashed()->find($value);
                                                        return $user ? $user->name : 'Usuario eliminado';
                                                    })
                                                    ->required()
                                            )
                                    ])
                                    ->heading('Mecánicos')
                                    ->hidden(function () {
                                        $user = auth()->user();
                                        return !(
                                            $user->can('create_user') &&
                                            $user->can('update_user') &&
                                            $user->can('delete_user')
                                        );
                                    }),
                                Section::make()
                                    ->schema([
                                        Repeater::make('archivos')
                                            ->defaultItems(0)
                                            ->relationship()
                                            ->simple(
                                                FileUpload::make('archivo_url')
                                                    ->directory('trabajo_archivo')
                                                    ->required()
                                            )
                                    ])
                                    ->heading('Archivos')
                                    ->hidden(true),
                            ])
                            ->columnspan(1)
                            ->columns(1),
                        Section::make()
                            ->schema([
                                Repeater::make('servicios')
                                    ->relationship()
                                    ->defaultItems(0)
                                    ->schema([
                                        Select::make('servicio_id')
                                            ->label('Servicio')
                                            ->options(Servicio::query()->withTrashed()->pluck('nombre', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->reactive()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $servicio = Servicio::find($state);
                                                if ($servicio) {
                                                    $set('precio', $servicio->costo);
                                                }
                                            })
                                            ->createOptionForm([
                                                TextInput::make('nombre')
                                                    ->unique(table: 'servicios', column: 'nombre', ignoreRecord: true)
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('costo')
                                                    ->numeric()
                                                    ->prefix('S/ ')
                                                    ->required()
                                                    ->maxValue(42949672.95),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                return Servicio::create($data)->getKey();
                                            })
                                            // ->editOptionForm([
                                            //     TextInput::make('nombre')
                                            //         ->unique(table: 'servicios', column: 'nombre', ignoreRecord: true)
                                            //         ->required()
                                            //         ->maxLength(255),
                                            //     TextInput::make('costo')
                                            //         ->numeric()
                                            //         ->prefix('S/ ')
                                            //         ->maxValue(42949672.95),
                                            // ])
                                            // ->getOptionLabelUsing(function ($value): ?string {
                                            //     $servicio = Servicio::withTrashed()->find($value);
                                            //     return $servicio ? $servicio->nombre : 'Servicio eliminado';
                                            // })
                                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 2, 'sm' => 1]),
                                        TextInput::make('precio')
                                            // ->reactive()
                                            ->numeric()
                                            ->prefix('S/ ')
                                            ->maxValue(42949672.95)
                                            ->required()
                                            ->dehydrated()
                                            // ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            //     $cantidad = $get('cantidad') ?? 0;
                                            //     $set('total', $state * $cantidad);
                                            // })
                                            ->columnSpan(1),
                                        TextInput::make('cantidad')
                                            // ->reactive()
                                            ->numeric()
                                            ->default('1')
                                            ->required()
                                            ->dehydrated()
                                            // ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            //     $precio = $get('precio') ?? 0;
                                            //     $set('total', $precio * $state);
                                            // })
                                            ->columnSpan(1),
                                        // TextInput::make('total')
                                        //     ->numeric()
                                        //     ->prefix('S/ ')
                                        //     ->disabled()
                                        //     ->columnSpan(1)
                                    ])
                                    ->orderColumn('sort')
                                    ->reorderableWithButtons()
                                    ->columns(5)
                                    ->hiddenLabel()
                            ])
                            ->heading('Servicios ejecutados')
                            ->hidden(function () {
                                $user = auth()->user();
                                return !(
                                    $user->can('create_servicio') &&
                                    $user->can('update_servicio') &&
                                    $user->can('delete_servicio')
                                );
                            })
                            ->hiddenOn('create'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_ingreso')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('vehiculo.placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('vehiculo.marca')
                    ->label('Marca')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('vehiculo.modelo')
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
                TextColumn::make('vehiculo.tipoVehiculo.nombre')
                    ->label('Tipo')
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
                    ->placeholder('Sin Mecánicos')
                    ->label('Mecánicos')
                    ->searchable(isIndividual: true)
                    ->badge()
                    ->wrap(),
                // TextColumn::make('taller.nombre')
                //     ->label('Taller')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('fecha_salida')
                //     ->date('d/m/Y')
                //     ->sortable(),
                TextColumn::make('fecha_salida')
                    ->label('Fecha salida')
                    ->state(function ($record) {
                        return $record->fecha_salida
                            ? Carbon::parse($record->fecha_salida)->format('d/m/Y')
                            : $record->taller->nombre;
                    }),
                SelectColumn::make('desembolso')
                    ->placeholder('')
                    ->options([
                        'A CUENTA' => 'A CUENTA',
                        'COBRADO' => 'COBRADO',
                        'POR COBRAR' => 'POR COBRAR',
                    ]),
                TextColumn::make('.getImporte')
                    ->label('Importe Total')
                    ->getStateUsing(function (Trabajo $record): string {
                        return number_format($record->getImporte(), 2, '.', '');
                    })
                    ->prefix('S/ ')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('.getACuenta')
                    ->label('A Cuenta')
                    ->getStateUsing(function (Trabajo $record): string {
                        return number_format($record->getACuenta(), 2, '.', '');
                    })
                    ->prefix('S/ ')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('.getPorCobrar')
                    ->label('Por cobrar')
                    ->getStateUsing(function (Trabajo $record): string {
                        return number_format($record->getPorCobrar(), 2, '.', '');
                    })
                    ->prefix('S/ ')
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
                TextColumn::make('deleted_at')
                    ->label('Fecha de eliminación')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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

                SelectFilter::make('desembolso')
                    ->label('Estado del Desembolso')
                    ->options([
                        'A CUENTA' => 'A Cuenta',
                        'COBRADO' => 'Cobrado',
                        'POR COBRAR' => 'Por Cobrar',
                    ])
                    ->placeholder('Todos'),
                DateRangeFilter::make('fecha_ingreso'),
                DateRangeFilter::make('fecha_salida'),
                SelectFilter::make('taller_id')
                    ->label('Taller')
                    ->relationship('taller', 'nombre')
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('terminar')
                    ->label('Terminar')
                    ->color('success')
                    ->icon('heroicon-s-check')
                    ->visible(fn(Trabajo $record) => is_null($record->fecha_salida)) // Visible solo si fecha_salida es null
                    ->action(function (Trabajo $record) {
                        $total = $record->getImporte();
                        $cuenta = $record->getACuenta();

                        if ($total == 0 || $cuenta == 0) {
                            $record->update([
                                'fecha_salida' => now(),
                                'desembolso' => 'POR COBRAR'
                            ]);
                        } elseif ($cuenta < $total) {
                            $record->update([
                                'fecha_salida' => now(),
                                'desembolso' => 'A CUENTA'
                            ]);
                        } elseif ($cuenta >= $total) {
                            $record->update([
                                'fecha_salida' => now(),
                                'desembolso' => 'COBRADO'
                            ]);
                        }

                        Notification::make()
                            ->title('El trabajo ha sido marcado como terminado.')
                            ->success()
                            ->send();
                    })
                    ->button(),
                Action::make('reabrir')
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
                    ->button(),
                ActionGroup::make([
                    Action::make('Descargar')
                        ->icon('heroicon-s-arrow-down-tray')
                        ->url(
                            fn(Trabajo $trabajo): string => route('trabajo.pdf.report', ['trabajo' => $trabajo]),
                            shouldOpenInNewTab: true
                        ),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
                    ->color('gray')
                    ->size(ActionSize::Small)
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
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
            EvidenciasRelationManager::class,
            PagosRelationManager::class,
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
}
