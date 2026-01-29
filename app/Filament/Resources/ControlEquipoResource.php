<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlEquipoResource\Pages;
use App\Filament\Resources\ControlEquipoResource\RelationManagers;
use App\Filament\Resources\ControlEquipoResource\RelationManagers\DetallesRelationManager;
use App\Models\ControlEquipo;
use App\Models\Equipo;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ControlEquipoResource extends Resource
{
    protected static ?string $model = ControlEquipo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check'; // Puedes cambiar el icono si quieres

    protected static ?string $navigationGroup = 'Equipos e Implementos'; // Grupo nuevo

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DateTimePicker::make('fecha')
                            ->label('Fecha')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                        TextInput::make('responsable_nombre')
                            ->label('Responsable')
                            ->readOnly()
                            ->dehydrated(false)
                            ->default(fn() => Auth::user()?->name),
                        Hidden::make('responsable_id')
                            ->default(fn() => Auth::id())
                            ->required(),
                        Select::make('equipo_id')
                            ->label('Equipo')
                            ->relationship('equipo', 'codigo')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->hiddenOn('edit')
                            ->afterStateUpdated(function (?int $state, Set $set) {
                                if ($state) {
                                    $equipo = Equipo::with('propietario')->find($state);

                                    if ($equipo?->propietario_id) {
                                        // Equipo con propietario
                                        $set('propietario_id', $equipo->propietario_id);
                                        $set('propietario_nombre', $equipo->propietario?->name);
                                    } else {
                                        // Equipo sin propietario
                                        $set('propietario_id', null);
                                        $set('propietario_nombre', 'No asignado');
                                    }
                                } else {
                                    // Quitaron la selección
                                    $set('propietario_id', null);
                                    $set('propietario_nombre', null);
                                }
                            }),
                        TextInput::make('equipo_codigo')
                            ->label('Equipo')
                            ->readOnly()
                            ->hiddenOn('create'),
                        TextInput::make('propietario_nombre')
                            ->label('Propietario')
                            ->readOnly()
                            ->dehydrated(false)
                            ->disabled(fn(Get $get) => blank($get('propietario_id')))
                            ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                // En edición: si no hay propietario, mostrar "No asignado"
                                if ($record && blank($record->propietario_id)) {
                                    $component->state('No asignado');
                                }
                            }),
                        Hidden::make('propietario_id'),
                    ])
                    ->columnSpan(1)
                    ->heading('Control'),
                Section::make()
                    ->schema([
                        FileUpload::make('evidencia_url')
                            ->label('Evidencia')
                            ->directory('control-equipos'),
                    ])
                    ->columnSpan(1)
                    ->heading('Detalles'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('equipo.codigo')
                    ->label('Equipo')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('propietario.name')
                    ->label('Propietario')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->placeholder('No asignado'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('asignacionPdf')
                    ->label('Control')
                    ->button()
                    ->size(ActionSize::Medium)
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => route('pdf.control_equipo.asignacion', $record)) // Ruta actualizada
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->button()
                    ->color('gray'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //      Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DetallesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListControlEquipos::route('/'),
            'create' => Pages\CreateControlEquipo::route('/create'),
            'edit' => Pages\EditControlEquipo::route('/{record}/edit'),
        ];
    }
}