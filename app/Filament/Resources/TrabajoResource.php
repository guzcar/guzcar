<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoResource\Pages;
use App\Filament\Resources\TrabajoResource\RelationManagers;
use App\Models\Cliente;
use App\Models\Trabajo;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrabajoResource extends Resource
{
    protected static ?string $model = Trabajo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('vehiculo_id')
                                    ->relationship('vehiculo', 'nombre_completo')
                                    ->searchable()
                                    ->required()
                                    ->createOptionForm([
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
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
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
                                    ->editOptionForm([
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
                                        Select::make('tipo_vehiculo_id')
                                            ->relationship('tipoVehiculo', 'nombre')
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
                                    ]),
                                Select::make('taller_id')
                                    ->relationship('taller', 'nombre')
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('ubicacion')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->editOptionForm([
                                        TextInput::make('nombre')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('ubicacion')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                DatePicker::make('fecha_ingreso')
                                    ->default(now())
                                    ->required(),
                                Textarea::make('descripcion_servicio')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->heading('Trabajo')
                            ->columnSpan(1),
                        Section::make()
                            ->schema([
                                Repeater::make('mecanicos')
                                    ->relationship('mecanicos') // Relación hacia TrabajoMecanico en el modelo Trabajo
                                    ->defaultItems(0)
                                    ->simple(
                                        Select::make('mecanico_id') // Campo mecanico_id
                                            ->label('Seleccionar Mecánico')
                                            ->relationship('mecanico', 'name', fn ($query) => $query->withTrashed()) // Relación hacia User desde TrabajoMecanico
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('email')
                                                    ->label('Correo electrónico')
                                                    ->unique(ignoreRecord: true)
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('password')
                                                    ->label('Contraseña')
                                                    ->password()
                                                    ->confirmed()
                                                    ->dehydrated(fn($state) => filled($state))
                                                    ->required()
                                                    ->minLength(8),
                                                TextInput::make('password_confirmation')
                                                    ->label('Confirmar contraseña')
                                                    ->password()
                                                    ->dehydrated(fn($state) => filled($state))
                                                    ->required()
                                                    ->minLength(8),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                $data['password'] = bcrypt($data['password']);
                                                return User::create($data)->getKey();
                                            })
                                            ->editOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('email')
                                                    ->label('Correo electrónico')
                                                    ->unique(ignoreRecord: true)
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('password')
                                                    ->label('Contraseña')
                                                    ->password()
                                                    ->confirmed()
                                                    ->dehydrated(fn($state) => filled($state))
                                                    ->minLength(8),
                                                TextInput::make('password_confirmation')
                                                    ->label('Confirmar contraseña')
                                                    ->password()
                                                    ->dehydrated(fn($state) => filled($state))
                                                    ->minLength(8),
                                            ])
                                            ->getOptionLabelUsing(function ($value): ?string {
                                                $user = User::withTrashed()->find($value);
                                                return $user ? $user->name : 'Usuario eliminado';
                                            })
                                            ->required()
                                    )
                            ])
                            ->heading('Mecánicos')
                            ->columnSpan(1)
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_ingreso')
                    ->date('l, d/m/Y')
                    ->sortable(),
                ColumnGroup::make('Vehículo', [
                    TextColumn::make('vehiculo.placa')
                        ->label('Placa')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('vehiculo.marca')
                        ->label('Marca')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('vehiculo.modelo')
                        ->label('Modelo')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('vehiculo.color')
                        ->label('Color')
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('vehiculo.tipoVehiculo.nombre')
                        ->label('Tipo')
                        ->sortable()
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('vehiculo.clientes.nombre')
                        ->searchable()
                        ->badge()
                        ->wrap()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                TextColumn::make('descripcion_servicio')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('usuarios.name')
                    ->label('Mecánicos')
                    ->searchable()
                    ->badge()
                    ->wrap(),
                TextColumn::make('taller.nombre')
                    ->label('Taller')
                    ->searchable()
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
                TextColumn::make('deleted_at')
                    ->label('Fecha de eliminación')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('taller_id')
                    ->label('Taller')
                    ->relationship('taller', 'nombre')
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
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
            'index' => Pages\ListTrabajos::route('/'),
            'create' => Pages\CreateTrabajo::route('/create'),
            'view' => Pages\ViewTrabajo::route('/{record}'),
            'edit' => Pages\EditTrabajo::route('/{record}/edit'),
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
