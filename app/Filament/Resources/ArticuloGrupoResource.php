<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ArticuloCluster;
use App\Filament\Resources\ArticuloGrupoResource\Pages;
use App\Filament\Resources\ArticuloGrupoResource\RelationManagers;
use App\Models\ArticuloGrupo;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticuloGrupoResource extends Resource
{
    protected static ?string $model = ArticuloGrupo::class;

    protected static ?int $navigationSort = 75;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $modelLabel = 'Grupos';

    protected static ?string $pluralModelLabel = 'Grupos';

    protected static ?string $navigationLabel = 'Grupos';

    protected static ?string $cluster = ArticuloCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->columnSpanFull(),
                ColorPicker::make('color')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                ColorColumn::make('color'),
                TextColumn::make('nombre')
                    ->searchable(isIndividual: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageArticuloGrupos::route('/'),
        ];
    }
}
