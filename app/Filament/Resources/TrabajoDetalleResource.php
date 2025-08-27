<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoDetalleResource\Pages;
use App\Filament\Resources\TrabajoDetalleResource\RelationManagers;
use App\Models\TrabajoDetalle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TrabajoDetalleResource extends Resource
{
    protected static ?string $model = TrabajoDetalle::class;

    protected static ?string $navigationGroup = 'Histórico';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $modelLabel = 'Detalle del trabajo';

    protected static ?string $pluralModelLabel = 'Detalles de los Trabajos';

    protected static ?string $navigationLabel = 'Detalles de los Trabajos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                ColumnGroup::make('Trabajo', [
                    TextColumn::make('trabajo.codigo')
                        ->label('Código')
                        ->searchable(isIndividual: true)
                        ->url(function (TrabajoDetalle $record): ?string {
                            if ($record->trabajo && auth()->user()->can('update_trabajo')) {
                                return TrabajoResource::getUrl('edit', ['record' => $record->trabajo]);
                            } elseif ($record->trabajo && auth()->user()->can('view_trabajo')) {
                                return TrabajoResource::getUrl('view', ['record' => $record->trabajo]);
                            }
                            return null;
                        })
                        ->color('primary'),
                    TextColumn::make('trabajo.fecha_ingreso')
                        ->label('Fecha de Ingreso')
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.fecha_salida')
                        ->label('Fecha de Salida')
                        ->placeholder('Sin Salida')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Vehiculo', [
                    TextColumn::make('trabajo.vehiculo.tipoVehiculo.nombre')
                        ->label('Tipo')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.placa')
                        ->label('Placa')
                        ->placeholder('Sin Placa')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.marca.nombre')
                        ->label('Marca')
                        ->placeholder('Sin Marca')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.modelo.nombre')
                        ->label('Modelo')
                        ->placeholder('Sin Modelo')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.vehiculo.color')
                        ->label('Color')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.vehiculo.clientes.nombre')
                        ->placeholder('Sin Clientes')
                        ->searchable(isIndividual: true)
                        ->badge()
                        ->wrap()
                        ->toggleable(isToggledHiddenByDefault: true)
                ]),
                ColumnGroup::make('Detalles del trabajo', [
                    TextColumn::make('descripcion')
                        ->html()
                        ->searchable(isIndividual: true)
                        ->sortable()
                ]),
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
            ->defaultSort('trabajo_fecha_ingreso', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListTrabajoDetalles::route('/'),
            // 'create' => Pages\CreateTrabajoDetalle::route('/create'),
            // 'edit' => Pages\EditTrabajoDetalle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->join('trabajos', 'trabajo_detalles.trabajo_id', '=', 'trabajos.id')
            ->select('trabajo_detalles.*', 'trabajos.fecha_ingreso as trabajo_fecha_ingreso');
    }
}
