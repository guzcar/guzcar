<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use App\Models\Servicio;
use App\Models\TrabajoServicio;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ServiciosRelationManager extends RelationManager
{
    protected static string $relationship = 'servicios';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('servicio_id')
                    ->relationship('servicio', 'nombre')
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('costo')
                            ->numeric()
                            ->required()
                            ->prefix('S/ ')
                            ->maxValue(42949672.95),
                    ])
                    ->editOptionForm([
                        TextInput::make('nombre')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('costo')
                            ->numeric()
                            ->required()
                            ->prefix('S/ ')
                            ->maxValue(42949672.95),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $servicio = Servicio::find($state);
                        if ($servicio) {
                            $set('precio', $servicio->costo);
                        }
                    })
                    ->columnSpanFull(),
                TextInput::make('precio')
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->required()
                    ->dehydrated(),
                TextInput::make('cantidad')
                    ->required()
                    ->numeric()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('servicio.nombre')
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('precio')
                    ->prefix('S/ ')
                    ->alignRight(),
                TextColumn::make('cantidad')
                    ->alignCenter(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (TrabajoServicio $record): string {
                        return number_format($record->precio * $record->cantidad, 2,'.','');
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Ejecutar Servicio'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
