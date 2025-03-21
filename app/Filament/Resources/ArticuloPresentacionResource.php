<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloPresentacionResource\Pages;
use App\Filament\Resources\ArticuloPresentacionResource\RelationManagers;
use App\Models\ArticuloPresentacion;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ArticuloPresentacionResource extends Resource
{
    protected static ?string $model = ArticuloPresentacion::class;

    protected static ?string $navigationGroup = 'Configuración de logística';

    protected static ?int $navigationSort = 170;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Presentación de artículo';

    protected static ?string $pluralModelLabel = 'Presentaciones de artículo';

    protected static ?string $navigationLabel = 'Presentaciones de artículo';

    protected static ?string $slug = 'articulo-presentaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de edición')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListArticuloPresentacions::route('/'),
            // 'create' => Pages\CreateArticuloPresentacion::route('/create'),
            // 'edit' => Pages\EditArticuloPresentacion::route('/{record}/edit'),
        ];
    }
}
