<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioEjecutadoResource\Pages;
use App\Filament\Resources\ServicioEjecutadoResource\RelationManagers;
use App\Models\TrabajoServicio;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class ServicioEjecutadoResource extends Resource
{
    protected static ?string $model = TrabajoServicio::class;

    protected static ?string $navigationGroup = 'Histórico';

    protected static ?int $navigationSort = 80;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Servicio ejecutado';

    protected static ?string $pluralModelLabel = 'Servicios ejecutados';

    protected static ?string $navigationLabel = 'Servicios ejecutados';

    protected static ?string $slug = 'servicios-ejecutados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make('Trabajo', [
                    TextColumn::make('trabajo.codigo')
                        ->label('Código')
                        ->searchable(isIndividual: true)->url(function (TrabajoServicio $record): ?string {
                            if (auth()->user()->can('update_trabajo')) {
                                return TrabajoResource::getUrl('edit', ['record' => $record->trabajo]);
                            } elseif (auth()->user()->can('view_trabajo')) {
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
                    TextColumn::make('trabajo.vehiculo.placa')
                        ->label('Placa')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.marca')
                        ->label('Marca')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.modelo')
                        ->label('Modelo')
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
                ColumnGroup::make('Servicio', [
                    TextColumn::make('servicio.nombre')
                        ->label('Nombre de servicio')
                        ->searchable(isIndividual: true)
                        ->wrap()
                        ->lineClamp(2),
                    TextColumn::make('precio')
                        ->prefix('S/ ')
                        ->alignRight()
                        ->visible(fn() => auth()->user()->can('update_servicio::ejecutado')),
                    TextColumn::make('cantidad')
                        ->alignCenter()
                        ->visible(fn() => auth()->user()->can('update_servicio::ejecutado')),
                    TextColumn::make('subtotal')
                        ->label('Subtotal')
                        ->prefix('S/ ')
                        ->alignRight()
                        ->state(function (TrabajoServicio $record): string {
                            return number_format($record->precio * $record->cantidad, 2, '.', '');
                        })
                        ->visible(fn() => auth()->user()->can('update_servicio::ejecutado'))
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                ValueRangeFilter::make('costo'),
            ])
            ->actions([
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
            'index' => Pages\ListServicios::route('/'),
            // 'create' => Pages\CreateServicio::route('/create'),
            // 'edit' => Pages\EditServicio::route('/{record}/edit'),
        ];
    }
}
