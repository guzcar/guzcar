<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoPagoResource\Pages;
use App\Filament\Resources\TrabajoPagoResource\RelationManagers;
use App\Models\TrabajoPago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class TrabajoPagoResource extends Resource
{
    protected static ?string $model = TrabajoPago::class;

    protected static ?string $navigationGroup = 'Histórico';

    protected static ?int $navigationSort = 51;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Pagos';

    protected static ?string $navigationLabel = 'Pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('trabajo_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('monto')
                    ->prefix('S/ ')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('fecha_pago')
                    ->required(),
                Forms\Components\TextInput::make('observacion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('detalle_id')
                    ->relationship('detalle', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                ColumnGroup::make('Pago', [
                    TextColumn::make('monto')
                        ->prefix('S/ ')
                        ->sortable(),
                    TextColumn::make('fecha_pago')
                        ->date('d/m/Y')
                        ->sortable(),
                    TextColumn::make('observacion')
                        ->wrap()
                        ->lineClamp(3)
                        ->searchable(isIndividual: true),
                    TextColumn::make('detalle.nombre')
                        ->numeric()
                        ->sortable()
                        ->searchable(isIndividual: true),
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
                ValueRangeFilter::make('monto'),
                DateRangeFilter::make('fecha_pago'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTrabajoPagos::route('/'),
            // 'create' => Pages\CreateTrabajoPago::route('/create'),
            // 'edit' => Pages\EditTrabajoPago::route('/{record}/edit'),
        ];
    }
}
