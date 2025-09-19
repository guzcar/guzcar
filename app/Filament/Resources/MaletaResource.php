<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaletaResource\Pages;
use App\Filament\Resources\MaletaResource\RelationManagers;
use App\Filament\Resources\MaletaResource\RelationManagers\DetallesRelationManager;
use App\Models\Maleta;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaletaResource extends Resource
{
    protected static ?string $model = Maleta::class;

    protected static ?string $navigationGroup = 'Herramientas';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 45;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('codigo')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('propietario_id')
                            ->relationship('propietario', 'name')
                            ->searchable()
                            ->preload()
                    ])
                    ->columns(2)
                    ->heading('Maleta')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('codigo')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('propietario.name')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->placeholder('Sin asignar'),
                TextColumn::make('detalles_count')
                    ->label('Herramientas')
                    ->counts('detalles')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('pdf')
                    ->label('Acta de entrega')
                    ->button()
                    ->size(ActionSize::Medium)
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Maleta $record) => route('pdf.maleta', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListMaletas::route('/'),
            'create' => Pages\CreateMaleta::route('/create'),
            'edit' => Pages\EditMaleta::route('/{record}/edit'),
        ];
    }
}
