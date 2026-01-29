<?php

namespace App\Filament\Resources\ContabilidadResource\RelationManagers;

use App\Models\TrabajoOtro;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OtrosRelationManager extends RelationManager
{
    protected static string $relationship = 'otros';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('descripcion')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),
                TextInput::make('cantidad')
                    ->default(1)
                    ->required()
                    ->numeric(),
                TextInput::make('precio')
                    ->numeric()
                    ->required()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->reorderable('sort')
            // ->defaultSort('sort', 'asc')
            ->columns([
                TextColumn::make('descripcion'),
                TextColumn::make('presupuesto')
                    ->sortable()
                    ->label('Presupuesto')
                    ->formatStateUsing(fn($state) => $state ? 'Incluido' : 'Excluido')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->alignCenter(),
                TextColumn::make('precio')
                    ->label('Precio')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->alignCenter(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (TrabajoOtro $record): string {
                        return number_format($record->precio * $record->cantidad, 2, '.', '');
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear otro'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('marcarComoSi')
                        ->icon('heroicon-o-check')
                        ->label('Incluir en el Presupuesto')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->presupuesto = true; // Cambiar a "SI"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('marcarComoNo')
                        ->icon('heroicon-o-x-mark')
                        ->label('Excluir del Presupuesto')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->presupuesto = false; // Cambiar a "NO"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
