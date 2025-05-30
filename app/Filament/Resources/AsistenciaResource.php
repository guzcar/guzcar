<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsistenciaResource\Pages;
use App\Filament\Resources\AsistenciaResource\RelationManagers;
use App\Models\Asistencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class AsistenciaResource extends Resource
{
    protected static ?string $model = Asistencia::class;

    protected static ?int $navigationSort = 115;

    protected static ?string $navigationGroup = 'Usuarios';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Trabajador')
                    ->searchable(isIndividual: true)
                    ->sortable(),
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                DateRangeFilter::make('created_at')
                    ->label('Fecha de registro'),
            ])
            ->actions([
                DeleteAction::make()
                // Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsistencias::route('/'),
            // 'create' => Pages\CreateAsistencia::route('/create'),
            // 'edit' => Pages\EditAsistencia::route('/{record}/edit'),
        ];
    }
}
