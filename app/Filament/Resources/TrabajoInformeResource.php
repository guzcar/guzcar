<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoInformeResource\Pages;
use App\Filament\Resources\TrabajoInformeResource\RelationManagers;
use App\Models\TrabajoInforme;
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

class TrabajoInformeResource extends Resource
{
    protected static ?string $model = TrabajoInforme::class;

    protected static ?string $navigationGroup = 'Histórico';

    protected static ?int $navigationSort = 75;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Informe del trabajo';

    protected static ?string $pluralModelLabel = 'Informes de los Trabajos';

    protected static ?string $navigationLabel = 'Informes de los Trabajos';

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
                        ->url(function (TrabajoInforme $record): ?string {
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
                ColumnGroup::make('Informes del trabajo', [
                    TextColumn::make('contenido')
                        ->html()
                        ->wrap()
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
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
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
            'index' => Pages\ListTrabajoInformes::route('/'),
            'create' => Pages\CreateTrabajoInforme::route('/create'),
            'edit' => Pages\EditTrabajoInforme::route('/{record}/edit'),
        ];
    }
}
