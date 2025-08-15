<?php

namespace App\Filament\Resources\ContabilidadResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DescuentosRelationManager extends RelationManager
{
    protected static string $relationship = 'descuentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('detalle')
                    ->required()
                    ->maxLength(255),
                TextInput::make('descuento')
                    ->numeric()
                    ->required()
                    ->suffix('%')
                    ->maxValue(42949672.95),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('detalle')
            ->columns([
                TextColumn::make('detalle'),
                TextColumn::make('descuento')
                    ->suffix('%'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Añadir descuento'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
