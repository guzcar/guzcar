<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlMaletaResource\Pages;
use App\Filament\Resources\ControlMaletaResource\RelationManagers;
use App\Filament\Resources\ControlMaletaResource\RelationManagers\DetallesRelationManager;
use App\Models\ControlMaleta;
use App\Models\Maleta;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ControlMaletaResource extends Resource
{
    protected static ?string $model = ControlMaleta::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Herramientas';

    protected static ?int $navigationSort = 45;

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
                        Select::make('maleta_id')
                            ->label('Maleta')
                            ->relationship('maleta', 'codigo')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->hiddenOn('edit')
                            ->afterStateUpdated(function (?int $state, Set $set) {
                                if ($state) {
                                    $maleta = Maleta::with('propietario')->find($state);

                                    if ($maleta?->propietario_id) {
                                        // Maleta con propietario
                                        $set('propietario_id', $maleta->propietario_id);
                                        $set('propietario_nombre', $maleta->propietario?->name);
                                    } else {
                                        // Maleta sin propietario
                                        $set('propietario_id', null);
                                        $set('propietario_nombre', 'No asignado');
                                    }
                                } else {
                                    // Quitaron la selección
                                    $set('propietario_id', null);
                                    $set('propietario_nombre', null);
                                }
                            }),
                        TextInput::make('maleta_codigo')
                            ->label('Maleta')
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
                            ->label('Evidencias')
                            ->directory('control-maletas')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles(),
                    ])
                    ->columnSpan(1)
                    ->heading('Detalles'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->striped()
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:m A')
                    ->sortable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:m A')
                    ->sortable(),

                TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('maleta.codigo')
                    ->label('Maleta')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('propietario.name')
                    ->label('Propietario')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->placeholder('No asignado'),

                ImageColumn::make('evidencia_url')
                    ->placeholder('Sin evidencias')
                    ->label('Evidencias')
                    ->circular()
                    ->stacked()
                    ->limit(3),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('asignacionPdf')
                    ->label('Control')
                    // ->label('Hoja de asignación')
                    ->button()
                    ->size(ActionSize::Medium)
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => route('pdf.control_maleta.asignacion', $record))
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
                //     Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListControlMaletas::route('/'),
            'create' => Pages\CreateControlMaleta::route('/create'),
            'edit' => Pages\EditControlMaleta::route('/{record}/edit'),
        ];
    }
}
