<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('descripcion')
                    ->columnSpanFull()
                    // ->toolbarButtons([
                    //     'blockquote',
                    //     'bold',
                    //     'bulletList',
                    //     'heading',
                    //     'italic',
                    //     'link',
                    //     'orderedList',
                    //     'redo',
                    //     'strike',
                    //     'table',
                    //     'undo',
                    // ])
                    ->extraInputAttributes(['style' => 'min-height: 60vh; max-height: 100vh; overflow-y: auto;'])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('descripcion')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('descripcion')
                    ->html(),
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('screen')
                    ->closeModalByClickingAway(false)
                    ->modalActions([
                        // Tables\Actions\Action::make('cancel')
                        //     ->label('Cancelar')
                        //     ->color('gray')
                        //     ->requiresConfirmation()
                        //     ->modalHeading('¿Seguro que quieres cancelar?')
                        //     ->modalSubheading('Los cambios se perderán.')
                        //     ->modalButton('Sí, cancelar')
                        //     ->action(function ($livewire, $arguments) {
                        //         $livewire->dispatch('close-modal'); // este no cierra en v3
                        //     }),
                        Tables\Actions\Action::make('create')
                            ->label('Guardar')
                            ->submit('create'),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('screen')
                    ->closeModalByClickingAway(false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
