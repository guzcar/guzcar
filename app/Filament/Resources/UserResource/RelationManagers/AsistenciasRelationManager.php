<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class AsistenciasRelationManager extends RelationManager
{
    protected static string $relationship = 'asistencias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_at')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Marca de tiempo')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('lat')
                    ->label('Latitud')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lng')
                    ->label('Longitud')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at_dia')
                    ->label('Fecha')
                    ->getStateUsing(fn($record) => $record->created_at?->translatedFormat('l d \d\e F \d\e Y')),
                TextColumn::make('created_at_hora')
                    ->label('Hora')
                    ->getStateUsing(fn($record) => $record->created_at?->format('h:i A')),
            ])
            ->filters([
                DateRangeFilter::make('created_at')
                    ->label('Fecha de registro'),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
