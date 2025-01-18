<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoPagoResource\Pages;
use App\Filament\Resources\TrabajoPagoResource\RelationManagers;
use App\Models\TrabajoPago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrabajoPagoResource extends Resource
{
    protected static ?string $model = TrabajoPago::class;

    protected static ?string $navigationGroup = 'Historial';

    protected static ?int $navigationSort = 50;

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
                Tables\Columns\TextColumn::make('trabajo.vehiculo.placa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('observacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('detalle.nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'create' => Pages\CreateTrabajoPago::route('/create'),
            'edit' => Pages\EditTrabajoPago::route('/{record}/edit'),
        ];
    }
}
