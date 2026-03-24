<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\RepuestoCluster;
use App\Models\Trabajo;
use App\Models\TrabajoOtro;
use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use App\Filament\Resources\RepuestoResource\Pages;
use App\Models\Repuesto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RepuestoResource extends Resource
{
    protected static ?string $model = Repuesto::class;

    protected static ?int $navigationSort = 70;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $modelLabel = 'Repuesto';
    protected static ?string $pluralModelLabel = 'Repuestos';
    protected static ?string $navigationLabel = 'Catálogo de Repuestos';
    protected static ?string $cluster = RepuestoCluster::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make('Información Principal')
                            ->schema([
                                Select::make('categoria_id')
                                    ->label('Categoría')
                                    ->relationship('categoria', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live() // Añadimos live() para que detecte cambios en tiempo real
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        // Si no hay categoría seleccionada, no hacemos nada
                                        if (!$state) {
                                            $set('codigo', null);
                                            return;
                                        }

                                        // Buscamos el último repuesto de ESTA categoría
                                        $ultimoRepuesto = Repuesto::where('categoria_id', $state)
                                            ->orderBy('id', 'desc')
                                            ->first();

                                        if ($ultimoRepuesto && $ultimoRepuesto->codigo) {
                                            // Asumimos que el código tiene el formato TEXTO-NUMERO (ej: REPELEC-050)
                                            $partes = explode('-', $ultimoRepuesto->codigo);

                                            // Si realmente tiene dos partes (prefijo y número)
                                            if (count($partes) == 2) {
                                                $prefijo = $partes[0];
                                                $numeroActual = (int) $partes[1];

                                                // Incrementamos el número
                                                $siguienteNumero = $numeroActual + 1;

                                                // Mantenemos el formato de ceros a la izquierda (ej: 051)
                                                // Usamos strlen para saber cuántos ceros tenía originalmente (ej: 050 tiene largo 3)
                                                $longitudNumero = strlen($partes[1]);
                                                $nuevoNumeroFormateado = str_pad($siguienteNumero, $longitudNumero, '0', STR_PAD_LEFT);

                                                $nuevoCodigo = $prefijo . '-' . $nuevoNumeroFormateado;
                                                $set('codigo', $nuevoCodigo);
                                            } else {
                                                // Si el código no tiene el guión, dejamos que el usuario lo escriba
                                                $set('codigo', null);
                                            }
                                        } else {
                                            // Si es el PRIMER repuesto de esta categoría
                                            // Buscamos el nombre de la categoría para crear un prefijo base
                                            $categoria = \App\Models\CategoriaRepuesto::find($state);
                                            if ($categoria) {
                                                // Tomamos las primeras 3 letras del nombre en mayúsculas como prefijo por defecto
                                                $prefijoSugerido = strtoupper(substr($categoria->nombre, 0, 3));
                                                $set('codigo', $prefijoSugerido . '-001');
                                            }
                                        }
                                    }),

                                TextInput::make('codigo')
                                    ->label('Código')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                TextInput::make('nombre')
                                    ->label('Nombre del repuesto')
                                    ->required(),

                                TextInput::make('cantidad')
                                    ->label('Cantidad / Stock')
                                    ->default(1)
                                    ->numeric()
                                    ->nullable(),
                            ])
                            ->columns(2)
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 1, 'sm' => 1]),

                        Section::make('Detalles Técnicos')
                            ->schema([
                                TextInput::make('marca_modelo')
                                    ->label('Marca / Modelo')
                                    ->nullable(),

                                TextInput::make('motor')
                                    ->label('Motor')
                                    ->nullable(),

                                TextInput::make('medidas_cod_oem')
                                    ->label('Medidas / Código OEM')
                                    ->nullable(),

                                TextInput::make('estado')
                                    ->label('Estado')
                                    ->nullable(),
                            ])
                            ->columns(2)
                            ->columnSpan(['xl' => 2, 'lg' => 2, 'md' => 1, 'sm' => 1]),

                        Section::make('Datos Adicionales')
                            ->schema([
                                Select::make('tecnico_id')
                                    ->label('Técnico Asignado')
                                    ->relationship('tecnico', 'name')
                                    ->searchable()
                                    ->preload(),

                                DatePicker::make('fecha')
                                    ->label('Fecha')
                                    ->nullable(),

                                Textarea::make('notas')
                                    ->label('Notas')
                                    ->columnSpanFull()
                                    ->nullable(),
                            ])
                            ->columns(2)
                            ->columnSpan('full'),
                    ])
                    ->columns(['xl' => 5, 'lg' => 5, 'md' => 1, 'sm' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Aquí está la magia que colorea la fila según el color de la categoría
            ->recordClasses(fn(Repuesto $record) => $record->categoria?->color ?: null)
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->wrap()
                    ->copyable()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->placeholder('Sin categoría')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('cantidad')
                    ->label('Stock')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('marca_modelo')
                    ->label('Marca/Modelo')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('tecnico.name')
                    ->label('Técnico')
                    ->placeholder('No asignado')
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
            ->defaultSort('id', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('categoria_id')
                    ->label('Filtrar por Categoría')
                    ->relationship('categoria', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('usar')
                    ->label('Usar')
                    ->icon('heroicon-o-check-circle') // Puedes cambiar el icono
                    ->color('success')
                    ->form([
                        Select::make('trabajo_id')
                            ->label('Trabajo en vehículo')
                            ->prefixIcon('heroicon-s-truck')
                            ->required()
                            ->options(function (Get $get) {
                                $hoy = now()->format('Y-m-d');
                                $ayer = now()->subDay()->format('Y-m-d');
                                $trabajoId = $get('trabajo_id');

                                return Trabajo::with(['vehiculo', 'vehiculo.tipoVehiculo'])
                                    ->where(function ($query) use ($hoy, $ayer, $trabajoId) {
                                        if ($trabajoId) {
                                            $query->where('id', $trabajoId);
                                        }

                                        $query->orWhere(function ($subQuery) use ($hoy, $ayer) {
                                            $subQuery->where('disponible', true)
                                                ->orWhereNull('fecha_salida')
                                                ->orWhereDate('fecha_salida', $hoy)
                                                ->orWhereDate('fecha_salida', $ayer);
                                        });
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->mapWithKeys(function ($trabajo) {
                                        $fechaHoraIngreso = $trabajo->fecha_ingreso;
                                        // Usamos optional() por si la fecha es null
                                        $formatoFecha = optional($fechaHoraIngreso)->isoFormat('D [de] MMMM [de] YYYY') ?? 'Sin fecha';
                                        $textoTiempo = optional($fechaHoraIngreso)->locale('es')->diffForHumans() ?? '';

                                        $partesVehiculo = array_filter([
                                            $trabajo->vehiculo->placa ?? '',
                                            $trabajo->vehiculo->tipoVehiculo->nombre ?? '',
                                            $trabajo->vehiculo->marca?->nombre,
                                            $trabajo->vehiculo->modelo?->nombre,
                                            $trabajo->vehiculo->color ?? ''
                                        ], 'strlen');

                                        $label = sprintf(
                                            "%s - Ingreso: %s (%s)",
                                            implode(' ', $partesVehiculo),
                                            $formatoFecha,
                                            $textoTiempo
                                        );

                                        return [$trabajo->id => $label];
                                    });
                            })
                            ->searchable()
                            ->preload(),

                        Select::make('user_id')
                            ->label('Técnico')
                            ->prefixIcon('heroicon-s-user-circle')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(), // Es opcional según indicaste

                        TextInput::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->default(fn(Repuesto $record) => $record->nombre), // Se llena solo con el nombre del repuesto

                        TextInput::make('precio')
                            ->label('Precio')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('cantidad')
                            ->label('Cantidad a usar')
                            ->numeric()
                            ->required()
                            ->default(fn(Repuesto $record) => $record->cantidad ?? 1)
                            // La regla maxValue impide que el usuario ponga un número mayor al stock
                            ->maxValue(fn(Repuesto $record) => $record->cantidad ?? 1)
                            ->minValue(1),
                    ])
                    ->action(function (array $data, Repuesto $record) {
                        // 1. Creamos el TrabajoOtro
                        TrabajoOtro::create([
                            'descripcion' => $data['descripcion'],
                            'precio' => $data['precio'],
                            'cantidad' => $data['cantidad'],
                            'trabajo_id' => $data['trabajo_id'],
                            'user_id' => $data['user_id'] ?? null,
                            // Los demás campos no se tocan, asumimos que la base de datos o un Observer les da su valor por defecto
                        ]);

                        // 2. Gestionamos el stock del repuesto
                        $stockActual = $record->cantidad ?? 1; // Si es null, asumimos que hay 1
                        $cantidadUsada = (int) $data['cantidad'];

                        if ($cantidadUsada >= $stockActual) {
                            // Se consumió todo el stock (o el único disponible si era null)
                            if ($record->cantidad !== null) {
                                $record->update(['cantidad' => 0]);
                            }
                            $record->delete(); // Soft delete: se va a la papelera
            
                            Notification::make()
                                ->title('Repuesto usado y agotado')
                                ->body('El repuesto se asignó al trabajo y fue enviado a la papelera al quedarse sin stock.')
                                ->success()
                                ->send();
                        } else {
                            // Aún queda stock
                            $record->update([
                                'cantidad' => $stockActual - $cantidadUsada
                            ]);

                            Notification::make()
                                ->title('Repuesto asignado con éxito')
                                ->success()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Asignar Repuesto a Trabajo'),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('cambiar_categoria')
                        ->label('Cambiar Categoría')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->form([
                            Select::make('categoria_id')
                                ->label('Categoría')
                                ->options(\App\Models\CategoriaRepuesto::pluck('nombre', 'id'))
                                ->searchable()
                                ->required()
                                ->placeholder('Selecciona una categoría')
                        ])
                        ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'categoria_id' => $data['categoria_id']
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->modalWidth(MaxWidth::Large),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => Pages\ListRepuestos::route('/'),
            'create' => Pages\CreateRepuesto::route('/create'),
            'edit' => Pages\EditRepuesto::route('/{record}/edit'),
        ];
    }
}