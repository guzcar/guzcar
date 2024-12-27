<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculoResource\Pages;
use App\Filament\Resources\VehiculoResource\RelationManagers;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('placa')
                    ->maxLength(7),
                Forms\Components\TextInput::make('marca')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('modelo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('color')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo_vehiculo_id')
                    ->relationship('tipoVehiculo', 'nombre')
                    ->required(),
                Repeater::make('propietarios')
                    ->relationship() // Relación configurada en el modelo Vehiculo
                    ->simple(
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->options(Cliente::query()->pluck('nombre', 'id')) // Llena las opciones con clientes
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('identificador')
                                    ->required()
                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                    ->maxLength(12),
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('identificador')
                                    ->required()
                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                    ->maxLength(12),
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Cliente::create($data)->getKey();
                            })
                            ->fillEditOptionActionFormUsing(function ($record) {
                                // Llena el formulario de edición con los datos del cliente relacionado
                                return [
                                    'identificador' => $record->cliente->identificador ?? '',
                                    'nombre' => $record->cliente->nombre ?? '',
                                ];
                            })
                    )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('modelo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipoVehiculo.nombre')
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListVehiculos::route('/'),
            'create' => Pages\CreateVehiculo::route('/create'),
            'edit' => Pages\EditVehiculo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
