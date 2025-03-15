<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalidaResource\Pages;
use App\Filament\Resources\SalidaResource\RelationManagers;
use App\Models\Articulo;
use App\Models\Trabajo;
use App\Models\TrabajoArticulo;
use App\Models\User;
use App\Services\FractionService;
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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class SalidaResource extends Resource
{
    protected static ?string $model = TrabajoArticulo::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 60;

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
                                DatePicker::make('fecha')
                                    ->required()
                                    ->default(now()),
                                TimePicker::make('hora')
                                    ->required()
                                    ->default(now()),
                                Select::make('movimiento')
                                    ->label('Movimiento')
                                    ->options([
                                        'cerrado' => 'Abrir y gastar',
                                        'abierto' => 'Gastar abierto',
                                    ])
                                    ->default('cerrado')
                                    ->required()
                                    ->placeholder('')
                                    ->hidden(fn($get) => !$get('articulo_id') || !Articulo::find($get('articulo_id'))?->fraccionable),
                                Select::make('articulo_id')
                                    ->label('Artículo')
                                    ->columnSpanFull()
                                    ->options(function () {
                                        return Articulo::with(['categoria', 'subCategoria.categoria', 'marca', 'unidad', 'presentacion'])
                                            ->get()
                                            ->mapWithKeys(function ($articulo) {
                                                // Campos que se concatenarán en el label
                                                $categoria = $articulo->categoria->nombre ?? null;
                                                $subCategoria = $articulo->subCategoria->nombre ?? null;
                                                $especificacion = $articulo->especificacion ?? null;
                                                $marca = $articulo->marca->nombre ?? null;
                                                $presentacion = $articulo->presentacion->nombre ?? null;
                                                $medida = $articulo->medida ?? null;
                                                $unidad = $articulo->unidad->nombre ?? null;
                                                $color = $articulo->color ?? null;

                                                // Construye el label dinámicamente
                                                $labelParts = [];
                                                if ($categoria)
                                                    $labelParts[] = $categoria;
                                                if ($subCategoria)
                                                    $labelParts[] = $subCategoria;
                                                if ($especificacion)
                                                    $labelParts[] = $especificacion;
                                                if ($marca)
                                                    $labelParts[] = $marca;
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

                                                return [$articulo->id => $label];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $articulo = Articulo::find($state);
                                        if ($articulo) {
                                            $set('precio', $articulo->costo);
                                            $set('stock', $articulo->stock);
                                            $set('fraccionable', $articulo->fraccionable);

                                            if ($articulo->fraccionable) {
                                                $set('abiertos', $articulo->abiertos);
                                            } else {
                                                $set('abiertos', null);
                                            }

                                            $ubicaciones = $articulo->articuloUbicaciones
                                                ->filter(function ($articuloUbicacion) {
                                                    return $articuloUbicacion->ubicacion !== null;
                                                })
                                                ->map(function ($articuloUbicacion) {
                                                    return $articuloUbicacion->ubicacion->codigo;
                                                });
                                            $set('ubicaciones', $ubicaciones->toArray());
                                        }
                                    }),
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
                                                        '1' => '1',
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
                                                    ->reactive()
                                                    ->required()
                                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                        if ($state !== null) {
                                                            $set('cantidad', $state);
                                                        }
                                                    }),
                                                Hidden::make('cantidad'),
                                                TextInput::make('precio')
                                                    ->label('Costo para el servicio')
                                                    ->required()
                                                    ->numeric()
                                                    ->prefix('S/ ')
                                                    ->maxValue(42949672.95)
                                                    ->dehydrated(),
                                            ];
                                        } else {
                                            return [
                                                TextInput::make('cantidad')
                                                    ->label('Cantidad')
                                                    ->required()
                                                    ->numeric(),
                                                TextInput::make('precio')
                                                    ->label('Costo para el servicio')
                                                    ->required()
                                                    ->numeric()
                                                    ->prefix('S/ ')
                                                    ->maxValue(42949672.95)
                                                    ->dehydrated(),
                                            ];
                                        }
                                    }),
                                Textarea::make('observacion')
                                    ->columnSpanFull(),
                            ])
                            ->heading('Salida de Inventario')
                            ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2])
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 3, 'sm' => 5]),
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
                                            ->options(function () {
                                                // Obtener la fecha actual
                                                $fechaActual = now()->format('Y-m-d'); // Formatear para comparar solo la fecha
                                    
                                                return Trabajo::with(['vehiculo'])
                                                    ->where(function ($query) use ($fechaActual) {
                                                        $query->whereNull('fecha_salida') // Filtra por trabajos sin fecha_salida
                                                            ->orWhereDate('fecha_salida', '>=', $fechaActual); // Filtra por fecha_salida igual a la fecha actual
                                                    })
                                                    ->get()
                                                    ->mapWithKeys(function ($trabajo) {
                                                        $codigo = $trabajo->codigo;
                                                        $placa = $trabajo->vehiculo->placa;
                                                        $tipo = $trabajo->vehiculo->tipoVehiculo->nombre;
                                                        $marca = $trabajo->vehiculo->marca;
                                                        $modelo = $trabajo->vehiculo->modelo;
                                                        $color = $trabajo->vehiculo->color;
                                                        $label = "{$placa} {$tipo} {$marca} {$modelo} {$color} ({$codigo})";
                                                        return [$trabajo->id => $label];
                                                    });
                                            })
                                            ->searchable()
                                            ->preload(),
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
                                                            return new HtmlString(
                                                                '<span class="text-gray-400 dark:text-gray-500">Sin ubicaciones</span>'
                                                            );
                                                        }

                                                        $badges = array_map(function ($ubicacion) {
                                                            return <<<HTML
                                                            <div class="flex w-max">
                                                                <span style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);" class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary">
                                                                    <span class="grid">
                                                                        <span class="truncate">
                                                                            $ubicacion
                                                                        </span>
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
                        ->searchable(isIndividual: true)
                        ->sortable(),
                    TextColumn::make('tecnico.name')
                        ->label('Recibe')
                        ->searchable(isIndividual: true)
                        ->sortable(),
                    ToggleColumn::make('confirmado')
                        ->alignCenter()
                        ->label('Confirmado')
                        ->onColor('success')
                        ->offColor('gray')
                        ->onIcon('heroicon-c-check')
                        ->offIcon('heroicon-c-x-mark'),
                ]),
                ColumnGroup::make('Artículo', [
                    TextColumn::make('articulo.categoria.nombre')
                        ->label('Artículo')
                        ->searchable(isIndividual: true)
                        ->sortable(),
                    TextColumn::make('articulo.marca.nombre')
                        ->label('Marca')
                        ->placeholder('Sin marca')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('articulo.subcategoria.nombre')
                        ->placeholder('Sin grado o número')
                        ->label('Grado / Número')
                        ->searchable(isIndividual: true)
                        ->sortable(),
                    TextColumn::make('articulo.especificacion')
                        ->label('Especificación')
                        ->placeholder('Sin especificación')
                        ->searchable(isIndividual: true)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('articulo.presentacion.nombre')
                        ->label('Presentación')
                        ->placeholder('Sin presentación')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('articulo.medida')
                        ->label('Medida')
                        ->placeholder('0.00')
                        ->alignCenter()
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('articulo.unidad.nombre')
                        ->label('Unidad')
                        ->placeholder('N/A')
                        ->alignCenter()
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('articulo.color')
                        ->label('Color')
                        ->placeholder('Sin color')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Salida', [
                    TextColumn::make('precio')
                        ->label('Precio en Servicio')
                        ->prefix('S/ ')
                        ->alignRight()
                        ->sortable(),
                    TextColumn::make('cantidad')
                        ->sortable()
                        ->formatStateUsing(function ($state) {
                            return FractionService::decimalToFraction((float) $state);
                        })
                        ->alignCenter(),
                ]),
                ColumnGroup::make('Trabajo', [
                    TextColumn::make('trabajo.codigo')
                        ->label('Código')
                        ->searchable(isIndividual: true)
                        ->url(function ($record) {
                            if ($record->trabajo) {
                                $url = TrabajoResource::getUrl('edit', ['record' => $record->trabajo->id]);
                                return "{$url}?activeRelationManager=2";
                            }
                            return null;
                        })
                        ->color('primary')
                        ->sortable()
                        ->placeholder('Sin trabajo'),
                    TextColumn::make('trabajo.fecha_ingreso')
                        ->label('Fecha ingreso')
                        ->sortable()
                        ->placeholder('Sin trabajo')
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.fecha_salida')
                        ->label('Fecha salida')
                        ->sortable()
                        ->placeholder('Sin trabajo')
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.vehiculo.placa')
                        ->label('Placa')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.tipoVehiculo.nombre')
                        ->label('Tipo')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.marca')
                        ->label('Marca')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.color')
                        ->label('Color')
                        ->placeholder('Sin Vehiculo')
                        ->searchable(isIndividual: true),
                ]),
                TextColumn::make('observación')
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
            'index' => Pages\ListSalida::route('/'),
            'create' => Pages\CreateSalida::route('/create'),
            'edit' => Pages\EditSalida::route('/{record}/edit'),
        ];
    }
}
