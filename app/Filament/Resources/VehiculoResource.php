<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculoResource\Pages;
use App\Filament\Resources\VehiculoResource\RelationManagers;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    protected static ?string $navigationGroup = 'Core';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $modelLabel = 'Vehículo';

    protected static ?string $pluralModelLabel = 'Vehículos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('tipo_vehiculo_id')
                                    ->label('Tipo de vehículo')
                                    ->relationship('tipoVehiculo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(50),
                                    ])
                                    ->editOptionForm([
                                        TextInput::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(50),
                                    ])
                                    ->required(),
                                TextInput::make('placa')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(7),
                                TextInput::make('marca')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('modelo')
                                    ->maxLength(255),
                                TextInput::make('color')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->heading('Vehículo')
                            ->columnSpan(1),
                        Section::make()
                            ->schema([
                                Repeater::make('propietarios')
                                    ->relationship()
                                    ->simple(
                                        Select::make('cliente_id')
                                            ->label('Seleccionar Cliente')
                                            ->relationship('cliente', 'nombre_completo', fn ($query) => $query->withTrashed())
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('identificador')
                                                    ->label('RUC / DNI')
                                                    ->required()
                                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                                    ->maxLength(12),
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                return Cliente::create($data)->getKey();
                                            })
                                            ->editOptionForm([
                                                TextInput::make('identificador')
                                                    ->label('RUC / DNI')
                                                    ->required()
                                                    ->unique(table: 'clientes', column: 'identificador', ignoreRecord: true)
                                                    ->maxLength(12),
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->getOptionLabelUsing(function ($value): ?string {
                                                $cliente = Cliente::withTrashed()->find($value);
                                                return $cliente ? $cliente->nombre_completo : 'Cliente eliminado';
                                            })
                                            ->required()
                                    )
                                    ->defaultItems(0)
                            ])
                            ->heading('Clientes')
                            ->columnSpan(1)
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('placa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('marca')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('modelo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipoVehiculo.nombre')
                    ->label('Tipo')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clientes.nombre')
                    ->searchable()
                    ->badge()
                    ->wrap(),
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
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
