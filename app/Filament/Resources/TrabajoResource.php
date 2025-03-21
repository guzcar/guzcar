<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoResource\Pages;
use App\Filament\Resources\TrabajoResource\RelationManagers;
use App\Filament\Resources\TrabajoResource\RelationManagers\EvidenciasRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\OtrosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\PagosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\ServiciosRelationManager;
use App\Filament\Resources\TrabajoResource\RelationManagers\TrabajoArticulosRelationManager;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Trabajo;
use App\Models\User;
use App\Services\TrabajoService;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
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
                                    ->minLength(16)
                                    ->maxLength(16),
                                Select::make('vehiculo_id')
                                    ->relationship('vehiculo')
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $placa = $record->placa ?? '';
                                        $tipoVehiculo = $record->tipoVehiculo->nombre;
                                        $marca = $record->marca;
                                        $modelo = $record->modelo;
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
                                                            ->initialCountry('pe')
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
                                                            ->initialCountry('pe')
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
                                            ->maxLength(7),
                                        TextInput::make('marca')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('modelo')
                                            ->maxLength(255),
                                        TextInput::make('color')
                                            ->required()
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
                                                            ->initialCountry('pe')
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
                                                            ->initialCountry('pe')
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
                                    ->hiddenOn('edit'),
                                Section::make()
                                    ->schema([
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
                                            ])
                                            ->collapsed()
                                            ->itemLabel(fn(array $state): ?string => $state['tecnico_id'] ? User::withTrashed()->find($state['tecnico_id'])->name : null)
                                    ])
                                    ->heading('Técnicos')
                                    ->hiddenOn('create'),
                                Section::make()
                                    ->schema([
                                        Repeater::make('archivos')
                                            ->label('')
                                            ->createItemButtonLabel('Añadir archivo')
                                            ->defaultItems(0)
                                            ->relationship()
                                            ->simple(
                                                FileUpload::make('archivo_url')
                                                    ->directory('trabajo_archivo')
                                                    ->required()
                                            )
                                    ])
                                    ->heading('Archivos')
                                    ->hiddenOn('create'),
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
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('fecha_ingreso')
                    ->date('d/m/Y')
                    ->sortable(),
                CheckboxColumn::make('control')
                    ->alignCenter()
                    ->hidden(fn() => !auth()->user()->can('update_trabajo')),
                TextColumn::make('vehiculo.placa')
                    ->label('Placa')
                    ->placeholder('SIN PLACA')
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
                    ->toggleable(isToggledHiddenByDefault: false),
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
                    ])
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('importe')
                    ->alignRight()
                    ->label('Importe total')
                    ->prefix('S/ ')
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('a_cuenta')
                    ->alignRight()
                    ->prefix('S/ ')
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago'))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('.getPorCobrar')
                    ->alignRight()
                    ->label('Por cobrar')
                    ->getStateUsing(function (Trabajo $record): string {
                        return number_format($record->getPorCobrar(), 2, '.', '');
                    })
                    ->prefix('S/ ')
                    ->hidden(fn() => !auth()->user()->can('view_trabajo::pago'))
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
            ->recordUrl(function (Trabajo $record): ?string {
                if (auth()->user()->can('update_trabajo')) {
                    return static::getUrl('edit', ['record' => $record]);
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
                DateRangeFilter::make('fecha_ingreso'),
                DateRangeFilter::make('fecha_salida'),
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
                            'fecha_salida' => now(),
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

                    Action::make('Descargar proforma')
                        ->icon('heroicon-s-document-text')
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
                        ->action(function (Trabajo $trabajo, array $data, $livewire) {

                            $params = [
                                'igv' => $data['igv'] ?? false,
                                'igv_porcentaje' => $data['igv_porcentaje'] ?? 18,
                                // 'servicios' => $data['servicios'] ?? true,
                                // 'articulos' => $data['articulos'] ?? true,
                            ];

                            $url = route('trabajo.pdf.report', ['trabajo' => $trabajo] + $params);
                            $livewire->js("window.open('{$url}', '_blank');");
                        })
                        ->modalHeading('Configuración de Descarga')
                        ->modalButton('Descargar')
                        ->modalWidth('md')
                        ->hidden(fn() => !auth()->user()->can('view_trabajo::pago')),

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
                    ->color('danger'),
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
            ServiciosRelationManager::class,
            TrabajoArticulosRelationManager::class,
            OtrosRelationManager::class,
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
