<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlEquipoDetalleResource\Pages;
use App\Models\ControlEquipoDetalle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ControlEquipoDetalleResource extends Resource
{
    protected static ?string $model = ControlEquipoDetalle::class;

    protected static ?string $navigationGroup = 'Equipos e Implementos';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Historial de Controles';

    protected static ?string $modelLabel = 'Historial de Controles';

    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('control.fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('control.propietario.name')
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->label('Técnico')
                    ->placeholder('No asignado'),
                Tables\Columns\TextColumn::make('implemento.nombre')
                    ->label('Implemento')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Caso')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'OPERATIVO' => 'success',
                        'MERMA' => 'warning',
                        'PERDIDO' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'MERMA' => 'Merma',
                        'PERDIDO' => 'Perdido',
                    ])
                    ->multiple(),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Filtramos para ver SOLO incidencias (Mermas y Pérdidas)
    // Si quisieras ver TODO el historial, comenta el whereIn
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('estado', ['MERMA', 'PERDIDO']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListControlEquipoDetalles::route('/'),
        ];
    }
}