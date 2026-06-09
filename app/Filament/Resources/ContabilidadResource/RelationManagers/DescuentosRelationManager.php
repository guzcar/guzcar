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
use Filament\Forms\Get; // <-- Necesario para obtener el estado actual
use Filament\Forms\Set; // <-- Necesario para modificar el otro campo

class DescuentosRelationManager extends RelationManager
{
    protected static string $relationship = 'descuentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('detalle')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(), // Hace que el detalle ocupe toda la fila

                // Campo Real (Porcentaje) que se guarda en Base de Datos
                TextInput::make('descuento')
                    ->label('Porcentaje (%)')
                    ->numeric()
                    ->required()
                    ->suffix('%')
                    ->maxValue(100) // Lo limitamos a 100% lógicamente
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, $state, $livewire) {
                        $trabajo = $livewire->getOwnerRecord();
                        $subtotal = $trabajo->getSubtotal();
                        
                        // Si actualizan el %, recalculamos el monto fijo
                        if ($subtotal > 0 && is_numeric($state)) {
                            $set('monto_fijo', round(($subtotal * $state) / 100, 2));
                        } else {
                            $set('monto_fijo', 0);
                        }
                    }),

                // Campo Virtual (Monto Fijo) para comodidad del usuario
                TextInput::make('monto_fijo')
                    ->label('Monto Descuento')
                    ->numeric()
                    ->prefix('S/') // Símbolo de moneda
                    ->dehydrated(false) // NO se envía a la base de datos
                    ->live(onBlur: true)
                    ->afterStateHydrated(function (TextInput $component, $record, $livewire) {
                        // Cuando se edita el registro, calculamos cuánto es el equivalente en moneda
                        if ($record && $record->descuento) {
                            $trabajo = $livewire->getOwnerRecord();
                            $subtotal = $trabajo->getSubtotal();
                            $component->state(round(($subtotal * $record->descuento) / 100, 2));
                        }
                    })
                    ->afterStateUpdated(function (Get $get, Set $set, $state, $livewire) {
                        $trabajo = $livewire->getOwnerRecord();
                        $subtotal = $trabajo->getSubtotal();
                        
                        // Si actualizan el monto fijo, calculamos a qué porcentaje equivale y llenamos el input real
                        if ($subtotal > 0 && is_numeric($state)) {
                            $set('descuento', round(($state / $subtotal) * 100, 4));
                        } else {
                            $set('descuento', 0);
                        }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('detalle')
            ->columns([
                TextColumn::make('detalle'),
                
                TextColumn::make('descuento')
                    ->label('Porcentaje')
                    ->suffix('%'),
                    
                TextColumn::make('monto_fijo_virtual')
                    ->label('Monto Descontado')
                    ->prefix('S/ ')
                    ->getStateUsing(function ($record) {
                        if (!$record->trabajo) return 0;
                        
                        $subtotal = $record->trabajo->getSubtotal();
                        return number_format(($subtotal * $record->descuento) / 100, 2);
                    }),
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