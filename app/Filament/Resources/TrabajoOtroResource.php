<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoOtroResource\Pages;
use App\Filament\Resources\TrabajoOtroResource\RelationManagers;
use App\Models\Trabajo;
use App\Models\TrabajoOtro;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TrabajoOtroResource extends Resource
{
    protected static ?string $model = TrabajoOtro::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 42;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $modelLabel = 'Salida directa';

    protected static ?string $pluralModelLabel = 'Salidas directas';

    protected static ?string $slug = 'salidas-directas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([

                                TextInput::make('descripcion')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('cantidad')
                                    ->required()
                                    ->numeric(),

                                TextInput::make('precio')
                                    ->required()
                                    ->label('Precio en servicio')
                                    ->numeric()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95),
                            ])
                            ->heading('Salida de Inventario')
                            ->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2])
                            ->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 3, 'sm' => 5]),

                        // Sección de detalles (stock, abiertos, ubicaciones)
                        Grid::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('trabajo_id')
                                            ->required()
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('descripcion')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('cantidad')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('precio')
                    ->prefix('S/ ')
                    ->sortable()
                    ->alignRight(),
                ColumnGroup::make('Trabajo', [
                    TextColumn::make('trabajo.codigo')
                        ->label('Código')
                        ->searchable(isIndividual: true)
                        ->url(function (TrabajoOtro $record): ?string {
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTrabajoOtros::route('/'),
            'create' => Pages\CreateTrabajoOtro::route('/create'),
            'edit' => Pages\EditTrabajoOtro::route('/{record}/edit'),
        ];
    }
}
