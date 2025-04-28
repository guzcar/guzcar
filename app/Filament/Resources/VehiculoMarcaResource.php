<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculoMarcaResource\Pages;
use App\Filament\Resources\VehiculoMarcaResource\RelationManagers;
use App\Filament\Resources\VehiculoMarcaResource\RelationManagers\ModelosRelationManager;
use App\Models\VehiculoMarca;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class VehiculoMarcaResource extends Resource
{
    protected static ?string $model = VehiculoMarca::class;

    protected static ?string $navigationGroup = 'Configuración de taller';

    protected static ?int $navigationSort = 200;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Marca de vehículo';

    protected static ?string $pluralModelLabel = 'Marcas de vehículo';

    protected static ?string $navigationLabel = 'Marcas de vehículo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
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
                ExportBulkAction::make(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ModelosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehiculoMarcas::route('/'),
            'create' => Pages\CreateVehiculoMarca::route('/create'),
            'edit' => Pages\EditVehiculoMarca::route('/{record}/edit'),
        ];
    }
}
