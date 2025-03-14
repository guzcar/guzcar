<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DespachoResource\Pages;
use App\Filament\Resources\DespachoResource\RelationManagers;
use App\Filament\Resources\DespachoResource\RelationManagers\TrabajoArticulosRelationManager;
use App\Models\Despacho;
use App\Models\Trabajo;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DespachoResource extends Resource
{
    protected static ?string $model = Despacho::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('codigo')
                                            ->default(now()->format('Ymdhis'))
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20)
                                            ->prefixIcon('heroicon-s-archive-box-arrow-down'),
                                        TextInput::make('responsable')
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
                                    ])->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                                Textarea::make('observacion')
                                    ->columnSpanFull(),
                            ])->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 3, 'sm' => 3]),
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

                            ])->columnSpan(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2])
                    ])->columns(['xl' => 5, 'lg' => 5, 'md' => 5, 'sm' => 5]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(isIndividual: true),
                TextColumn::make('trabajo.codigo')
                    ->placeholder('Sin trabajo')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->url(function ($record) {
                        if ($record->trabajo) {
                            $url = TrabajoResource::getUrl('edit', ['record' => $record->trabajo->id]);
                            return "{$url}?activeRelationManager=2";
                        }
                        return null;
                    })
                    ->color('primary'),
                TextColumn::make('responsable.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tecnico.name')
                    ->label('Técnico')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('articulos_count')
                    ->label('Salidas')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('hora')
                    ->time('h:i A')
                    ->sortable(),
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
                DateRangeFilter::make('fecha')
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('downloadPdf')
                        ->label('Descargar')
                        ->icon('heroicon-s-arrow-down-tray')
                        ->url(fn(Despacho $record) => route('despachos.pdf', $record->id))
                        ->openUrlInNewTab(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->button()
                    ->color('gray'),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('trabajoArticulos as articulos_count');
    }

    public static function getRelations(): array
    {
        return [
            TrabajoArticulosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDespachos::route('/'),
            'create' => Pages\CreateDespacho::route('/create'),
            'edit' => Pages\EditDespacho::route('/{record}/edit'),
        ];
    }
}
