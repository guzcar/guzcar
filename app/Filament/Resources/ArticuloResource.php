<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages;
use App\Filament\Resources\ArticuloResource\RelationManagers;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
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

class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Artículo';

    protected static ?string $pluralModelLabel = 'Artículos';

    protected static ?string $navigationLabel = 'Artículos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('categoria_id')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Categoría')
                    ->options(Categoria::all()->pluck('nombre', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('sub_categoria_id', null)),

                Forms\Components\Select::make('sub_categoria_id')
                    ->required()
                    ->label('Subcategoría')
                    ->placeholder('')
                    ->options(function ($get) {
                        $categoriaId = $get('categoria_id');
                        if ($categoriaId) {
                            return SubCategoria::where('categoria_id', $categoriaId)->pluck('nombre', 'id');
                        }
                        return [];
                    })
                    ->disabled(fn($get) => !$get('categoria_id')),

                TextInput::make('marca')
                    ->required(),
                TextInput::make('tamano_presentacion')
                    ->label('Tamaño / Presentación')
                    ->required(),
                TextInput::make('precio')
                    ->required()
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95),
                Textarea::make('descripcion')
                    ->label('Descripción')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subCategoria.categoria.nombre')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('subCategoria.nombre')
                    ->sortable()
                    ->searchable(isIndividual: true),
                TextColumn::make('marca')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tamano_presentacion')
                    ->label('Tamaño')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('precio')
                    ->prefix('S/ ')
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticulos::route('/'),
            'create' => Pages\CreateArticulo::route('/create'),
            'edit' => Pages\EditArticulo::route('/{record}/edit'),
        ];
    }
}
