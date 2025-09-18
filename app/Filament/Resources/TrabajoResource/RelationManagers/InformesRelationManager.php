<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use App\Models\TrabajoInformePlantilla;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InformesRelationManager extends RelationManager
{
    protected static string $relationship = 'informes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('plantilla_id')
                    ->label('Seleccionar plantilla')
                    ->options(TrabajoInformePlantilla::pluck('nombre', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $plantilla = TrabajoInformePlantilla::find($state);
                            if ($plantilla) {
                                $set('contenido', $plantilla->contenido);
                            }
                        } else {
                            $set('contenido', '');
                        }
                    })
                    ->placeholder('Elige una plantilla...')
                    ->columnSpanFull()
                    ->hiddenOn('edit'),

                Forms\Components\RichEditor::make('contenido')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'blockquote',
                        'bold',
                        'bulletList',
                        'heading',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'table',
                        'undo',
                    ])
                    ->extraInputAttributes(['class' => 'max-h-96', 'style' => 'overflow-y: scroll;'])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contenido')
                    ->wrap()
                    ->html()
                    ->limit(100),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('screen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('screen'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}