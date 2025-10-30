<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalidaResource\Pages;
use App\Filament\Resources\SalidaResource\RelationManagers;
use App\Models\Articulo;
use App\Models\Trabajo;
use App\Models\TrabajoArticulo;
use App\Models\User;
use App\Services\FractionService;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class SalidaResource extends Resource
{
    protected static ?string $model = TrabajoArticulo::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $modelLabel = 'Salida';

    protected static ?string $pluralModelLabel = 'Salidas';

    protected static ?string $slug = 'salidas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                // Responsable (solo lectura)
                                TextInput::make('responsable')
                                    ->label('Responsable de entrega')
                                    ->required()
                                    ->readOnly()
                                    ->prefixIcon('heroicon-s-user-circle')
                                    ->afterStateHydrated(function (TextInput $component, $context) {
                                        if ($context === 'create') {
                                            $userName = Auth::user()->name;
                                            $component->state($userName);
                                        }
                                    }),

                                // Fecha y hora
                                DatePicker::make('fecha')
                                    ->required()
                                    ->default(now()),
                                TimePicker::make('hora')
                                    ->required()
                                    ->default(now()),

                                // Movimiento (solo para artículos fraccionables)
                                Select::make('movimiento')
                                    ->label('Movimiento')
                                    ->options([
                                        'consumo_completo' => 'Consumo completo',
                                        'abrir_nuevo' => 'Abrir nuevo',
                                        'terminar_abierto' => 'Terminar abierto',
                                        'consumo_parcial' => 'Consumo parcial',
                                    ])
                                    ->default('consumo_completo')
                                    ->required()
                                    ->placeholder('')
                                    ->hidden(fn($get) => !$get('articulo_id') || !Articulo::find($get('articulo_id'))?->fraccionable),

                                // Artículo (deshabilitado en edición)
                                Select::make('articulo_id')
                                    ->required()
                                    ->label('Artículo')
                                    ->columnSpanFull()
                                    ->options(function () {
                                        return Articulo::with(['categoria', 'subCategoria.categoria', 'marca', 'unidad', 'presentacion'])
                                            ->get()
                                            ->mapWithKeys(function ($articulo) {
                                                $labelParts = array_filter([
                                                    $articulo->categoria->nombre ?? null,
                                                    $articulo->marca->nombre ?? null,
                                                    $articulo->subCategoria->nombre ?? null,
                                                    $articulo->especificacion ?? null,
                                                    $articulo->presentacion->nombre ?? null,
                                                    $articulo->medida ?? null,
                                                    $articulo->unidad->nombre ?? null,
                                                    $articulo->color ?? null,
                                                ]);
                                                $label = implode(' ', $labelParts);
                                                return [$articulo->id => $label];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->disabled(fn($context) => $context === 'edit') // Deshabilitar en edición
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $articulo = Articulo::find($state);
                                        if ($articulo) {
                                            $set('stock', $articulo->stock);

                                            // Usar precio si existe y no es null/0, de lo contrario usar costo
                                            $precio = $articulo->precio && $articulo->precio > 0 ? $articulo->precio : $articulo->costo;
                                            $set('precio', $precio);

                                            $set('fraccionable', $articulo->fraccionable);
                                            $set('abiertos', $articulo->fraccionable ? $articulo->abiertos : null);

                                            $ubicaciones = $articulo->articuloUbicaciones
                                                ->filter(fn($articuloUbicacion) => $articuloUbicacion->ubicacion !== null)
                                                ->map(fn($articuloUbicacion) => $articuloUbicacion->ubicacion->codigo);
                                            $set('ubicaciones', $ubicaciones->toArray());
                                        }
                                    }),

                                TextInput::make('precio')
                                    ->label('Precio en servicio')
                                    ->numeric()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95),

                                // Cantidad (dependiendo de si es fraccionable)
                                Grid::make()
                                    ->schema(function ($get) {
                                        $fraccionable = $get('fraccionable');
                                        if ($fraccionable) {
                                            return [
                                                Select::make('cantidad_fraccion')
                                                    ->label('Cantidad fraccionada')
                                                    ->options([
                                                        '0.25' => '1/4',
                                                        '0.50' => '1/2',
                                                        '0.75' => '3/4',
                                                        '1.00' => '1',
                                                        'custom' => 'Ingresar valor exacto',
                                                    ])
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                        if ($state !== 'custom') {
                                                            $set('cantidad', $state);
                                                            $set('cantidad_custom', null);
                                                        }
                                                    }),
                                                TextInput::make('cantidad_custom')
                                                    ->label('Cantidad exacta')
                                                    ->numeric()
                                                    ->hidden(fn($get) => $get('cantidad_fraccion') !== 'custom')
                                                    // ->reactive()
                                                    ->required()
                                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                        if ($state !== null) {
                                                            $set('cantidad', $state);
                                                        }
                                                    }),
                                                Hidden::make('cantidad')
                                                    ->rules([
                                                        function (Forms\Get $get) {
                                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                                $movimiento = $get('movimiento');
                                                                $stockDisponible = $get('stock');
                                                                $abiertos = $get('abiertos');

                                                                switch ($movimiento) {
                                                                    case 'consumo_completo':
                                                                        if ($value > $stockDisponible) {
                                                                            $fail("La cantidad no puede ser mayor al stock disponible ($stockDisponible).");
                                                                            Notification::make()
                                                                                ->title('Error')
                                                                                ->body("La cantidad no puede ser mayor al stock disponible ($stockDisponible).")
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                        break;
                                                                    case 'abrir_nuevo':
                                                                        if ($value >= 1) {
                                                                            $fail("La cantidad debe ser menor a 1.");
                                                                            Notification::make()
                                                                                ->title('Error')
                                                                                ->body("La cantidad debe ser menor a 1.")
                                                                                ->danger()
                                                                                ->send();
                                                                        } elseif ($stockDisponible < 1) {
                                                                            $fail("No hay suficiente stock para abrir un nuevo artículo.");
                                                                            Notification::make()
                                                                                ->title('Error')
                                                                                ->body("No hay suficiente stock para abrir un nuevo artículo.")
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                        break;
                                                                    case 'terminar_abierto':
                                                                        if ($abiertos < 1) {
                                                                            $fail("No hay artículos abiertos para terminar.");
                                                                            Notification::make()
                                                                                ->title('Error')
                                                                                ->body("No hay artículos abiertos para terminar.")
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                        break;
                                                                    case 'consumo_parcial':
                                                                        if ($value >= 1) {
                                                                            $fail("La cantidad debe ser menor a 1.");
                                                                            Notification::make()
                                                                                ->title('Error')
                                                                                ->body("La cantidad debe ser menor a 1.")
                                                                                ->danger()
                                                                                ->send();
                                                                        } elseif ($abiertos < 1) {
                                                                            $fail("No hay artículos abiertos para gastar.");
                                                                            Notification::make()
                                                                                ->title('Error')
                                                                                ->body("No hay artículos abiertos para gastar.")
                                                                                ->danger()
                                                                                ->send();
                                                                        }
                                                                        break;
                                                                }
                                                            };
                                                        },
                                                    ]),
                                            ];
                                        } else {
                                            return [
                                                TextInput::make('cantidad')
                                                    ->label('Cantidad')
                                                    ->required()
                                                    ->numeric()
                                                    ->rules([
                                                        function (Forms\Get $get) {
                                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                                $stockDisponible = $get('stock');
                                                                if ($value > $stockDisponible) {
                                                                    $fail("La cantidad no puede ser mayor al stock disponible ($stockDisponible).");
                                                                }
                                                            };
                                                        },
                                                    ]),
                                            ];
                                        }
                                    }),

                                // Observación
                                Textarea::make('observacion')
                                    ->columnSpanFull(),
                            ])
                            ->heading('Salida de Inventario')
                            ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2])
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 3, 'sm' => 5]),

                        // Sección de detalles (stock, abiertos, ubicaciones)
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('tecnico_id')
                                            ->label('Técnico que recibe')
                                            ->prefixIcon('heroicon-s-user-circle')
                                            ->options(User::all()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('trabajo_id')
                                            ->label('Trabajo en vehículo')
                                            ->prefixIcon('heroicon-s-truck')
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
                                                        // Solución segura para combinar fecha (date) + hora (time)
                                                        $fechaHoraIngreso = $trabajo->fecha_ingreso;

                                                        // Formateo de fecha (opcional: puedes usar ->format() si prefieres)
                                                        $formatoFecha = $fechaHoraIngreso->isoFormat('D [de] MMMM [de] YYYY');
                                                        $textoTiempo = $fechaHoraIngreso->locale('es')->diffForHumans();

                                                        // Construcción del label seguro con valores nulos
                                                        $partesVehiculo = array_filter([
                                                            $trabajo->vehiculo->placa,
                                                            $trabajo->vehiculo->tipoVehiculo->nombre,
                                                            $trabajo->vehiculo->marca?->nombre,
                                                            $trabajo->vehiculo->modelo?->nombre,
                                                            $trabajo->vehiculo->color
                                                        ], 'strlen');

                                                        $label = sprintf(
                                                            "%s\nIngreso: %s (%s)",
                                                            implode(' ', $partesVehiculo),
                                                            $formatoFecha,
                                                            $textoTiempo
                                                        );

                                                        return [$trabajo->id => $label];
                                                    });
                                            })
                                            ->searchable()
                                            ->preload()
                                    ]),
                                Section::make()
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                Placeholder::make('stock')
                                                    ->content(fn($get) => $get('stock'))
                                                    ->columnSpan(['xl' => 1, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                                                Placeholder::make('abiertos')
                                                    ->content(function ($get) {
                                                        $value = $get('abiertos');
                                                        return FractionService::decimalToFraction((float) $value);
                                                    })
                                                    ->columnSpan(['xl' => 1, 'lg' => 2, 'md' => 2, 'sm' => 2])
                                                    ->hidden(fn($get) => !$get('fraccionable')),
                                                Placeholder::make('ubicaciones')
                                                    ->label('Ubicación')
                                                    ->content(function ($get) {
                                                        $ubicaciones = $get('ubicaciones');
                                                        if (empty($ubicaciones)) {
                                                            return new HtmlString('<span class="text-gray-400 dark:text-gray-500">Sin ubicaciones</span>');
                                                        }
                                                        $badges = array_map(function ($ubicacion) {
                                                            return <<<HTML
                                                            <div class="flex w-max">
                                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary">
                                                                    <span class="grid">
                                                                        <span class="truncate">$ubicacion</span>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                            HTML;
                                                        }, $ubicaciones);
                                                        return new HtmlString('<div class="flex gap-1.5 flex-wrap">' . implode('', $badges) . '</div>');
                                                    })
                                                    ->columnSpan(['xl' => 2, 'lg' => 4, 'md' => 4, 'sm' => 4]),
                                            ])
                                            ->columns(['xl' => 4, 'lg' => 4, 'md' => 4, 'sm' => 4])
                                    ])
                                    ->hidden(fn($get) => !$get('articulo_id')),
                            ])
                            ->columnSpan(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 5])
                    ])
                    ->columns(['xl' => 5, 'lg' => 5, 'md' => 5, 'sm' => 5]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50])
            ->columns([
                ColumnGroup::make('Resposables', [
                    TextColumn::make('fecha')
                        ->date('d/m/Y')
                        ->sortable(),
                    TextColumn::make('hora')
                        ->time('H:i A')
                        ->sortable(),
                    TextColumn::make('responsable.name')
                        ->label('Entrega')
                        ->formatStateUsing(fn(string $state): string => explode(' ', $state)[0])
                        ->searchable(isIndividual: true)
                        ->sortable(),
                    TextColumn::make('tecnico.name')
                        ->label('Recibe')
                        ->formatStateUsing(fn(string $state): string => explode(' ', $state)[0])
                        ->searchable(isIndividual: true)
                        ->sortable(),
                    ToggleColumn::make('confirmado')
                        ->alignCenter()
                        ->label('Confirmado')
                        ->onColor('success')
                        ->hidden(fn() => !auth()->user()->can('create_trabajo'))
                        ->offColor('gray')
                        ->onIcon('heroicon-c-check')
                        ->offIcon('heroicon-c-x-mark'),
                ]),
                ColumnGroup::make('Artículo', [
                    ColorColumn::make('articulo.grupo.color')
                        ->alignCenter()
                        ->placeholder('S.G.')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('articulo')
                        ->label('Descripción del artículo')
                        ->state(function (TrabajoArticulo $record) {
                            $articulo = $record->articulo;

                            // Campos que se concatenarán en el label
                            $categoria = $articulo->categoria->nombre ?? null; // Acceder directamente a la categoría
                            $marca = $articulo->marca->nombre ?? null; // Acceder al nombre de la marca
                            $subCategoria = $articulo->subCategoria->nombre ?? null;
                            $especificacion = $articulo->especificacion ?? null;
                            $presentacion = $articulo->presentacion->nombre ?? null; // Acceder al nombre de la presentación
                            $medida = $articulo->medida ?? null;
                            $unidad = $articulo->unidad->nombre ?? null; // Acceder al nombre de la unidad
                            $color = $articulo->color ?? null;

                            // Construye el label dinámicamente
                            $labelParts = [];
                            if ($categoria)
                                $labelParts[] = $categoria;
                            if ($marca)
                                $labelParts[] = $marca;
                            if ($subCategoria)
                                $labelParts[] = $subCategoria;
                            if ($especificacion)
                                $labelParts[] = $especificacion;
                            if ($presentacion)
                                $labelParts[] = $presentacion;
                            if ($medida)
                                $labelParts[] = $medida;
                            if ($unidad)
                                $labelParts[] = $unidad;
                            if ($color)
                                $labelParts[] = $color;

                            // Une las partes con un espacio
                            $label = implode(' ', $labelParts);

                            return $label;
                        }),
                    TextColumn::make('articulo.categoria.nombre')
                        ->label('Artículo')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.marca.nombre')
                        ->label('Marca')
                        ->placeholder('Sin marca')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.subcategoria.nombre')
                        ->placeholder('Sin grado o número')
                        ->label('Grado / Número')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.especificacion')
                        ->label('Especificación')
                        ->placeholder('Sin especificación')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.presentacion.nombre')
                        ->label('Presentación')
                        ->placeholder('Sin presentación')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.medida')
                        ->label('Medida')
                        ->placeholder('0.00')
                        ->alignCenter()
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.unidad.nombre')
                        ->label('Unidad')
                        ->placeholder('N/A')
                        ->alignCenter()
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('articulo.color')
                        ->label('Color')
                        ->placeholder('Sin color')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Salida', [
                    TextColumn::make('cantidad')
                        ->sortable()
                        ->formatStateUsing(function ($state) {
                            return FractionService::decimalToFraction((float) $state);
                        })
                        ->alignCenter(),
                    TextColumn::make('precio')
                        ->label('P. Unitario')
                        ->prefix('S/ ')
                        ->alignRight()
                        ->hidden(fn() => !auth()->user()->can('create_contabilidad'))
                        ->sortable(),
                    TextColumn::make('total')
                        ->label('P. Total')
                        ->state(fn(TrabajoArticulo $record): float => $record->precio * $record->cantidad)
                        ->numeric()
                        ->formatStateUsing(fn(float $state): string => 'S/ ' . number_format($state, 2))
                        ->alignRight()
                        ->sortable(query: fn(Builder $query, string $direction): Builder => $query->orderByRaw('(precio * cantidad) ' . $direction)),
                ]),
                ColumnGroup::make('Trabajo', [
                    TextColumn::make('trabajo.codigo')
                        ->label('Código')
                        ->searchable(isIndividual: true)
                        ->url(function (TrabajoArticulo $record): ?string {
                            if ($record->trabajo && auth()->user()->can('update_trabajo')) {
                                return TrabajoResource::getUrl('edit', ['record' => $record->trabajo]);
                            } elseif ($record->trabajo && auth()->user()->can('view_trabajo')) {
                                return TrabajoResource::getUrl('view', ['record' => $record->trabajo]);
                            }
                            return null;
                        })
                        ->color('primary')
                        ->sortable()
                        ->placeholder('Sin trabajo'),
                    TextColumn::make('trabajo.fecha_ingreso')
                        ->date('d/m/Y')
                        ->label('Fecha ingreso')
                        ->sortable()
                        ->placeholder('Sin ingreso')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('trabajo.fecha_salida')
                        ->state(function ($record) {
                            if ($record->trabajo) {
                                return $record->trabajo->fecha_salida
                                    ? Carbon::parse($record->trabajo->fecha_salida)->format('d/m/Y')
                                    : ($record->trabajo->taller ? $record->trabajo->taller->nombre : 'Sin taller');
                            }
                            return null;
                        })
                        ->label('Fecha salida')
                        ->placeholder('Sin salida')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('trabajo.vehiculo.placa')
                        ->label('Placa')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.tipoVehiculo.nombre')
                        ->label('Tipo')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.marca.nombre')
                        ->label('Marca')
                        ->placeholder('Sin Marca')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.modelo.nombre')
                        ->label('Modelo')
                        ->placeholder('Sin Modelo')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.color')
                        ->label('Color')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                ]),
                TextColumn::make('observacion')
                    ->placeholder('Sin observación')
                    ->extraAttributes(['style' => 'width: 15rem'])
                    ->lineClamp(2)
                    ->wrap()
                    ->searchable()
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('sin_confirmar')
                    ->label('Sin confirmar')
                    ->query(fn(Builder $query) => $query->where('confirmado', false)),
                DateRangeFilter::make('fecha'),
                ValueRangeFilter::make('precio'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
            ])
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession();
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
            'index' => Pages\ListSalida::route('/'),
            'create' => Pages\CreateSalida::route('/create'),
            'edit' => Pages\EditSalida::route('/{record}/edit'),
        ];
    }
}
