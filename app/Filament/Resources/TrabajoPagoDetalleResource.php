<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajoPagoDetalleResource\Pages;
use App\Filament\Resources\TrabajoPagoDetalleResource\RelationManagers;
use App\Models\TrabajoPagoDetalle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TrabajoPagoDetalleResource extends Resource
{
    protected static ?string $model = TrabajoPagoDetalle::class;

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 200;

    protected static ?string $modelLabel = 'Detalles de pago';

    protected static ?string $pluralModelLabel = 'Detalles de pagos';

    protected static ?string $navigationLabel = 'Detalles de pagos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
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
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
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
            'index' => Pages\ListTrabajoPagoDetalles::route('/'),
            // 'create' => Pages\CreateTrabajoPagoDetalle::route('/create'),
            // 'edit' => Pages\EditTrabajoPagoDetalle::route('/{record}/edit'),
        ];
    }
}
