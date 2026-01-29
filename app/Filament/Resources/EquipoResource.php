<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipoResource\Pages;
use App\Filament\Resources\EquipoResource\RelationManagers;
use App\Filament\Resources\EquipoResource\RelationManagers\DetallesRelationManager;
use App\Models\Equipo;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipoResource extends Resource
{
    protected static ?string $model = Equipo::class;

    protected static ?string $navigationGroup = 'Equipos e Implementos'; // Nuevo grupo

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 60;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('codigo')
                                            ->label('Código de Equipo')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        Select::make('propietario_id')
                                            ->label('Propietario')
                                            ->relationship('propietario', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Textarea::make('observacion')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(1),
                                FileUpload::make('evidencia')
                                    ->directory('equipos-evidencia')
                                    ->columnSpan(1)
                            ])
                            ->columns(2),
                    ])
                    ->columns(2)
                    ->heading('Datos del Equipo')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('propietario.name')
                    ->label('Propietario')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->placeholder('Sin asignar'),
                TextColumn::make('detalles_count')
                    ->label('Implementos')
                    ->counts('detalles') // Cuenta la relación
                    ->badge()
                    ->sortable(),
                ImageColumn::make('evidencia')
                    ->placeholder('Sin Evidencia')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->color('gray')
                    ->button(),
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
            DetallesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipos::route('/'),
            'create' => Pages\CreateEquipo::route('/create'),
            'edit' => Pages\EditEquipo::route('/{record}/edit'),
        ];
    }
}