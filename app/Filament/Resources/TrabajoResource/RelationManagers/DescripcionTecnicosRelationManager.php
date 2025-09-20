<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DescripcionTecnicosRelationManager extends RelationManager
{
    protected static string $relationship = 'descripcionTecnicos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Técnico')
                    ->default(Auth::user()->id)
                    ->relationship('user', 'name')
                    ->searchable()
                    ->disabledOn('edit')
                    ->preload(),
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
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('descripcion')
                    ->sortable()
                    ->copyable()
                    ->html(),
                TextColumn::make('user.name')
                    ->label('Técnico')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button(),
                // ActionGroup::make([
                //     Tables\Actions\EditAction::make(),
                //     Tables\Actions\DeleteAction::make(),
                // ])
                //     ->button()
                //     ->color('gray'),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
