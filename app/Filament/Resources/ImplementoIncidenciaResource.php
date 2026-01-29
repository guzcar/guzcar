<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImplementoIncidenciaResource\Pages;
use App\Models\ImplementoIncidencia;
use App\Models\Implemento;
use App\Models\Equipo;
use App\Models\EquipoDetalle;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ImplementoIncidenciaResource extends Resource
{
    protected static ?string $model = ImplementoIncidencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Equipos e Implementos';
    protected static ?int $navigationSort = 150;
    protected static ?string $modelLabel = 'Incidencia / Merma';

    public static function form(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('fecha')
                ->label('Fecha')
                ->seconds(false)
                ->default(now())
                ->required(),

            // Solo UI - Responsable
            TextInput::make('responsable_nombre')
                ->label('Responsable')
                ->default(fn() => Auth::user()?->name)
                ->readOnly()
                ->dehydrated(false),

            // --- SELECTOR DE TIPO DE ORIGEN (CREATE) ---
            Select::make('tipo_origen')
                ->label('Origen de la incidencia')
                ->options([
                    'EQUIPO' => 'Desde Equipo', // Ojo: EQUIPO en lugar de MALETA
                    'STOCK' => 'Desde Stock/Almacén',
                ])
                ->native(false)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Set $set) {
                    // Limpiar campos al cambiar tipo
                    $set('equipo_id', null);
                    $set('equipo_detalle_id', null);
                    $set('implemento_id', null);
                    $set('cantidad', 1);
                    $set('propietario_id', null);
                    $set('propietario_nombre', null);
                })
                ->hiddenOn('edit'),

            // --- TIPO DE ORIGEN (EDIT - Solo lectura) ---
            TextInput::make('tipo_origen_display')
                ->label('Tipo de incidencia')
                ->readOnly()
                ->dehydrated(false)
                ->afterStateHydrated(function (TextInput $component, $record) {
                    if ($record) {
                        $component->state($record->tipo_origen === 'EQUIPO' ? 'Desde Equipo' : 'Desde Stock/Almacén');
                    }
                })
                ->visibleOn('edit'),

            // ========== SECCIÓN PARA TIPO EQUIPO ==========

            // --- EQUIPO (CREATE) ---
            Select::make('equipo_id')
                ->label('Equipo')
                ->options(fn() => Equipo::query()
                    ->orderBy('codigo')
                    ->pluck('codigo', 'id'))
                ->searchable()
                ->preload()
                ->reactive()
                ->dehydrated(false)
                ->visible(fn(Get $get) => $get('tipo_origen') === 'EQUIPO')
                ->required(fn(Get $get) => $get('tipo_origen') === 'EQUIPO')
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state) {
                        $equipo = Equipo::with('propietario')->find($state);

                        // Propietario según equipo elegido
                        if ($equipo?->propietario_id) {
                            $set('propietario_id', $equipo->propietario_id);
                            $set('propietario_nombre', $equipo->propietario?->name);
                        } else {
                            $set('propietario_id', null);
                            $set('propietario_nombre', 'No asignado');
                        }
                    } else {
                        $set('propietario_id', null);
                        $set('propietario_nombre', null);
                    }

                    // Limpiar implemento al cambiar equipo
                    $set('equipo_detalle_id', null);
                })
                ->hiddenOn('edit'),

            // --- EQUIPO (EDIT) ---
            TextInput::make('equipo_codigo')
                ->label('Equipo')
                ->readOnly()
                ->dehydrated(false)
                ->visible(
                    fn($record, $operation) =>
                    $operation === 'edit' &&
                    $record &&
                    $record->tipo_origen === 'EQUIPO'
                ),

            // --- PROPIETARIO (UI) ---
            TextInput::make('propietario_nombre')
                ->label('Propietario')
                ->readOnly()
                ->dehydrated(false)
                ->visible(
                    fn(Get $get, $record, $operation) =>
                    ($operation === 'create' && $get('tipo_origen') === 'EQUIPO') ||
                    ($operation === 'edit' && $record && $record->tipo_origen === 'EQUIPO')
                )
                ->afterStateHydrated(function (TextInput $component, $state, $record) {
                    if ($record && $record->tipo_origen === 'EQUIPO' && blank($record->propietario_id)) {
                        $component->state('No asignado');
                    } elseif ($record && $record->tipo_origen === 'STOCK') {
                        $component->state('N/A');
                    }
                }),
            Hidden::make('propietario_id'),

            // --- IMPLEMENTO DESDE EQUIPO (CREATE) ---
            Select::make('equipo_detalle_id')
                ->label('Implemento')
                ->options(function (Get $get) {
                    $equipoId = $get('equipo_id');
                    if (!$equipoId) {
                        return [];
                    }

                    return EquipoDetalle::query()
                        ->with('implemento')
                        ->where('equipo_id', $equipoId)
                        ->whereNull('deleted_at')
                        ->orderByDesc('id')
                        ->get()
                        ->mapWithKeys(fn($ed) => [
                            $ed->id => $ed->implemento?->nombre
                                ? $ed->implemento->nombre
                                : "Detalle #{$ed->id}",
                        ])
                        ->toArray();
                })
                ->reactive()
                ->searchable()
                ->disabled(fn(Get $get) => blank($get('equipo_id')))
                ->required(fn(Get $get) => $get('tipo_origen') === 'EQUIPO' && filled($get('equipo_id')))
                ->dehydrated(fn(Get $get) => $get('tipo_origen') === 'EQUIPO')
                ->visible(fn(Get $get) => $get('tipo_origen') === 'EQUIPO')
                ->hiddenOn('edit'),

            // ========== SECCIÓN PARA TIPO STOCK ==========

            // --- IMPLEMENTO DESDE STOCK (CREATE) ---
            Select::make('implemento_id')
                ->label('Implemento')
                ->options(function () {
                    return Implemento::query()
                        ->where('stock', '>', 0)
                        ->orderBy('nombre')
                        ->get()
                        ->mapWithKeys(fn($i) => [
                            $i->id => "{$i->nombre} (Stock: {$i->stock})"
                        ])
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->required(fn(Get $get) => $get('tipo_origen') === 'STOCK')
                ->dehydrated(fn(Get $get) => $get('tipo_origen') === 'STOCK')
                ->visible(fn(Get $get) => $get('tipo_origen') === 'STOCK')
                ->reactive()
                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    if ($state && $get('tipo_origen') === 'STOCK') {
                        $implemento = Implemento::find($state);
                        $set('max_cantidad', $implemento?->stock ?? 0);
                    }
                })
                ->hiddenOn('edit'),

            // --- CANTIDAD (SOLO PARA STOCK) ---
            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->minValue(1)
                ->maxValue(fn(Get $get) => $get('max_cantidad') ?? 999999)
                ->default(1)
                ->required(fn(Get $get) => $get('tipo_origen') === 'STOCK')
                ->dehydrated(fn(Get $get) => $get('tipo_origen') === 'STOCK')
                ->visible(fn(Get $get) => $get('tipo_origen') === 'STOCK')
                ->helperText(
                    fn(Get $get) =>
                    $get('max_cantidad')
                    ? "Máximo disponible: {$get('max_cantidad')}"
                    : null
                )
                ->hiddenOn('edit'),

            Hidden::make('max_cantidad')->dehydrated(false),

            // --- IMPLEMENTO (EDIT - para ambos tipos) ---
            TextInput::make('implemento_nombre')
                ->label('Implemento')
                ->readOnly()
                ->dehydrated(false)
                ->visibleOn('edit'),

            // --- CANTIDAD (EDIT - solo mostrar si es STOCK) ---
            TextInput::make('cantidad_display')
                ->label('Cantidad')
                ->readOnly()
                ->dehydrated(false)
                ->visible(
                    fn($record, $operation) =>
                    $operation === 'edit' &&
                    $record &&
                    $record->tipo_origen === 'STOCK'
                )
                ->afterStateHydrated(function (TextInput $component, $record) {
                    if ($record && $record->tipo_origen === 'STOCK') {
                        $component->state($record->cantidad);
                    }
                }),

            // Hidden para equipo_detalle_id en edit
            Hidden::make('equipo_detalle_id')
                ->visibleOn('edit'),

            // --- MOTIVO ---
            Select::make('motivo')
                ->label('Motivo')
                ->options([
                    'MERMA' => 'MERMA',
                    'PERDIDO' => 'PERDIDO',
                ])
                ->native(false)
                ->required()
                ->hiddenOn('edit')
                ->dehydrated(true),

            // --- MOTIVO (EDIT - readonly) ---
            TextInput::make('motivo_display')
                ->label('Motivo')
                ->readOnly()
                ->dehydrated(false)
                ->visibleOn('edit')
                ->afterStateHydrated(function (TextInput $component, $record) {
                    if ($record) {
                        $component->state($record->motivo);
                    }
                }),

            // --- OBSERVACIÓN ---
            Textarea::make('observacion')
                ->label('Observación')
                ->rows(3)
                ->maxLength(1000),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),

                TextColumn::make('tipo_origen')
                    ->label('Origen')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'EQUIPO' => 'info',
                        'STOCK' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'EQUIPO' => 'Equipo',
                        'STOCK' => 'Stock',
                    })
                    ->sortable(),

                TextColumn::make('implemento_display')
                    ->label('Implemento')
                    ->wrap()
                    ->getStateUsing(function ($record) {
                        if ($record->tipo_origen === 'EQUIPO') {
                            $ed = $record->equipoDetalle()
                                ->withTrashed()
                                ->with('implemento')
                                ->first();
                            return $ed?->implemento?->nombre
                                ?? "Detalle #{$record->equipo_detalle_id}";
                        } else {
                            return $record->implemento?->nombre
                                ?? "Implemento #{$record->implemento_id}";
                        }
                    })
                    ->searchable(
                        query: function (Builder $query, string $search) {
                            $query->where(function ($q) use ($search) {
                                // Buscar en equipo_detalles
                                $q->whereHas('equipoDetalle', function ($q2) use ($search) {
                                    $q2->withTrashed()
                                        ->whereHas('implemento', function ($q3) use ($search) {
                                            $q3->where('nombre', 'like', "%{$search}%");
                                        });
                                })
                                    // O buscar directo en implementos (stock)
                                    ->orWhereHas('implemento', function ($q2) use ($search) {
                                        $q2->where('nombre', 'like', "%{$search}%");
                                    });
                            });
                        },
                        isIndividual: true
                    ),

                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('propietario.name')
                    ->label('Propietario')
                    ->placeholder('No corresponde')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'MERMA' => 'warning',
                        'PERDIDO' => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(isIndividual: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_origen')
                    ->label('Tipo')
                    ->options([
                        'EQUIPO' => 'Desde Equipo',
                        'STOCK' => 'Desde Stock',
                    ]),
                Tables\Filters\SelectFilter::make('motivo')
                    ->options([
                        'MERMA' => 'MERMA',
                        'PERDIDO' => 'PERDIDO',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'propietario',
            'responsable',
            'implemento',
            'equipoDetalle' => fn($q) => $q->withTrashed()->with(['implemento', 'equipo']),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImplementoIncidencias::route('/'),
            'create' => Pages\CreateImplementoIncidencia::route('/create'),
            'edit' => Pages\EditImplementoIncidencia::route('/{record}/edit'),
        ];
    }
}