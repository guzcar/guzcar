<?php

namespace App\Filament\Resources\ContabilidadResource\RelationManagers;

use App\Models\Servicio;
use App\Models\TrabajoServicio;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ServiciosRelationManager extends RelationManager
{
    protected static string $relationship = 'servicios';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('servicio_id')
                    ->relationship(
                        name: 'servicio',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn(Builder $query) => $query->with('tipoVehiculo')
                    )
                    ->getOptionLabelFromRecordUsing(function (Servicio $record) {
                        return $record->tipoVehiculo
                            ? "{$record->tipoVehiculo->nombre} - {$record->nombre}"
                            : $record->nombre;
                    })
                    ->createOptionForm([
                        Grid::make()
                            ->schema([
                                Select::make('tipo_vehiculo_id')
                                    ->label('Tipo de Vehículo')
                                    ->relationship('tipoVehiculo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1),
                                TextInput::make('costo')
                                    ->numeric()
                                    ->required()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                        Textarea::make('nombre')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->editOptionForm([
                        Grid::make()
                            ->schema([
                                Select::make('tipo_vehiculo_id')
                                    ->label('Tipo de Vehículo')
                                    ->relationship('tipoVehiculo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1),
                                TextInput::make('costo')
                                    ->numeric()
                                    ->required()
                                    ->prefix('S/ ')
                                    ->maxValue(42949672.95)
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                        Textarea::make('nombre')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
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
                    ->columnSpanFull(),
                Textarea::make('detalle')
                    ->columnSpanFull(),
                TextInput::make('precio')
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->required()
                    ->dehydrated(),
                TextInput::make('cantidad')
                    ->default(1)
                    ->required()
                    ->numeric()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort', 'asc')
            ->columns([
                TextColumn::make('servicio.nombre')
                    ->wrap()
                    ->lineClamp(3),
                TextColumn::make('detalle')
                    ->wrap()
                    ->lineClamp(3)
                    ->placeholder('Sin detalles'),
                TextColumn::make('presupuesto')
                    ->sortable()
                    ->label('Presupuesto')
                    ->formatStateUsing(fn($state) => $state ? 'Incluido' : 'Excluido')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->alignCenter(),
                TextColumn::make('precio')
                    ->prefix('S/ ')
                    ->alignRight(),
                TextColumn::make('cantidad')
                    ->alignCenter(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (TrabajoServicio $record): string {
                        return number_format($record->precio * $record->cantidad, 2, '.', '');
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Ejecutar Servicio'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('marcarComoSi')
                        ->icon('heroicon-o-check')
                        ->label('Incluir en el Presupuesto')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->presupuesto = true; // Cambiar a "SI"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('marcarComoNo')
                        ->icon('heroicon-o-x-mark')
                        ->label('Excluir del Presupuesto')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->presupuesto = false; // Cambiar a "NO"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
