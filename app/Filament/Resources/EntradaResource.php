<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntradaResource\Pages;
use App\Filament\Resources\EntradaResource\RelationManagers;
use App\Filament\Resources\EntradaResource\RelationManagers\EntradaArticulosRelationManager;
use App\Models\Entrada;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EntradaResource extends Resource
{
    protected static ?string $model = Entrada::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('guia')
                                            ->label('Guía')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20)
                                            ->prefixIcon('heroicon-s-document-text'),
                                        TextInput::make('responsable')
                                            ->required()
                                            ->readOnly()
                                            ->prefixIcon('heroicon-s-user-circle')
                                            ->afterStateHydrated(function (TextInput $component, $context) {
                                                if ($context === 'create') {
                                                    $userName = Auth::user()->name;
                                                    $component->state($userName);
                                                }
                                            }),
                                        DatePicker::make('fecha')
                                            ->required()
                                            ->default(now()),
                                        TimePicker::make('hora')
                                            ->required()
                                            ->default(now())
                                    ])->columns(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2]),
                                Textarea::make('observacion')
                                    ->label('Observación')
                                    ->rows(4),
                            ])->columnSpan(['xl' => 3, 'lg' => 3, 'md' => 3, 'sm' => 3]),
                        Section::make()
                            ->schema([
                                FileUpload::make('evidencia_url')
                                    ->label('Evidencia')
                                    ->directory('entrada')
                                    ->columnSpan(1)
                                    ->maxSize(500 * 1024),
                            ])
                            ->columnSpan(['xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2])
                    ])->columns(['xl' => 5, 'lg' => 5, 'md' => 5, 'sm' => 5]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guia')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('responsable.name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('articulos_count')
                    ->label('Ítems')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('hora')
                    ->time('h:i A')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->recordUrl(function (Entrada $record): string {
                return static::getUrl('edit', ['record' => $record]);
            })
            ->filters([
                DateRangeFilter::make('fecha'),
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
                    ->button()
                    ->color('gray'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('articulos');
    }

    public static function getRelations(): array
    {
        return [
            EntradaArticulosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntradas::route('/'),
            'create' => Pages\CreateEntrada::route('/create'),
            'view' => Pages\ViewEntrada::route('/{record}'),
            'edit' => Pages\EditEntrada::route('/{record}/edit'),
        ];
    }
}
