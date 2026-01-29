<?php

namespace App\Filament\Resources\ImplementoEntradaResource\RelationManagers;

use App\Models\Implemento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $title = 'Implementos Ingresados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('implemento_id')
                    ->columnSpanFull()
                    ->label('Implemento')
                    ->relationship('implemento', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live() // Reactividad para jalar el costo
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Al seleccionar implemento, buscamos su costo referencial
                        $costo = Implemento::find($state)?->costo ?? null;
                        if (! is_null($costo)) {
                            $set('costo', $costo);
                        }
                    }),

                TextInput::make('cantidad')
                    ->label('Cantidad a Ingresar')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),

                TextInput::make('costo')
                    ->label('Costo Unitario')
                    ->numeric()
                    ->rule('decimal:0,2')
                    ->required()
                    ->prefix('S/'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('implemento_id')
            ->columns([
                TextColumn::make('implemento.nombre')
                    ->label('Implemento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cantidad')
                    ->label('Cant.')
                    ->sortable(),

                TextColumn::make('costo')
                    ->label('Costo Unit.')
                    ->numeric(decimalPlaces: 2)
                    ->prefix('S/')
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->getStateUsing(fn ($record) => (float) $record->cantidad * (float) $record->costo)
                    ->numeric(decimalPlaces: 2)
                    ->prefix('S/')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Item'),
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