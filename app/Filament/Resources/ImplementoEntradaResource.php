<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImplementoEntradaResource\Pages;
use App\Filament\Resources\ImplementoEntradaResource\RelationManagers;
use App\Filament\Resources\ImplementoEntradaResource\RelationManagers\DetallesRelationManager;
use App\Models\ImplementoEntrada;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ImplementoEntradaResource extends Resource
{
    protected static ?string $model = ImplementoEntrada::class;

    protected static ?string $navigationGroup = 'Equipos e Implementos';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack'; // Icono sugerido para "Entrada"

    protected static ?string $navigationLabel = 'Entradas de Stock';

    protected static ?int $navigationSort = 95;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('codigo')
                    ->label('Código de Ingreso')
                    ->required()
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('fecha')
                    ->seconds(false)
                    ->default(now())
                    ->required(),
                TextInput::make('responsable_nombre')
                    ->label('Responsable')
                    ->readOnly()
                    ->dehydrated(false)
                    ->default(fn() => Auth::user()?->name),
                Hidden::make('responsable_id')
                    ->default(fn() => Auth::id())
                    ->required(),
                Textarea::make('observacion')
                    ->columnSpanFull(),
                FileUpload::make('evidencia_url')
                    ->label('Documento de Sustento (Factura/Guía)')
                    ->directory('implemento-entradas'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('codigo')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('responsable.name')
                    ->label('Registrado por')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
                TextColumn::make('detalles_count')
                    ->label('Items')
                    ->counts('detalles')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DetallesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImplementoEntradas::route('/'),
            'create' => Pages\CreateImplementoEntrada::route('/create'),
            'edit' => Pages\EditImplementoEntrada::route('/{record}/edit'),
        ];
    }
}