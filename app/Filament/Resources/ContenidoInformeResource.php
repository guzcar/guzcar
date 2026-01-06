<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContenidoInformeResource\Pages;
use App\Filament\Resources\ContenidoInformeResource\RelationManagers;
use App\Models\ContenidoInforme;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContenidoInformeResource extends Resource
{
    protected static ?string $model = ContenidoInforme::class;

    protected static ?string $navigationGroup = 'Configuración de logística';

    protected static ?int $navigationSort = 149;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Contenido de informes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('contenido')
                    ->columnSpanFull()
                    ->required()
                    ->rows(10)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('informe'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListContenidoInformes::route('/'),
            // 'create' => Pages\CreateContenidoInforme::route('/create'),
            // 'edit' => Pages\EditContenidoInforme::route('/{record}/edit'),
        ];
    }
}
