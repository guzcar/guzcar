<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaMaletaResource\Pages;
use App\Filament\Resources\EntregaMaletaResource\RelationManagers;
use App\Models\EntregaMaleta;
use App\Models\Maleta;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntregaMaletaResource extends Resource
{
    protected static ?string $model = EntregaMaleta::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Herramientas';
    protected static ?string $navigationLabel = 'Entregas de Maleta';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make()
                    ->schema([

                        Section::make('Información de Entrega')
                            ->schema([
                                DateTimePicker::make('fecha')
                                    ->label('Fecha de Entrega')
                                    ->seconds(false)
                                    ->default(now())
                                    ->required(),

                                TextInput::make('responsable_nombre')
                                    ->label('Responsable (Entrega)')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->default(fn() => Auth::user()?->name),

                                Hidden::make('responsable_id')
                                    ->default(fn() => Auth::id())
                                    ->required(),

                                Select::make('maleta_id')
                                    ->disabledOn('edit')
                                    ->label('Maleta')
                                    ->relationship('maleta', 'codigo')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (?int $state, Set $set) {
                                        if ($state) {
                                            $maleta = Maleta::with('propietario')->find($state);
                                            $set('propietario_id', $maleta?->propietario_id);
                                            $set('propietario_nombre', $maleta?->propietario?->name ?? 'Sin Asignar');
                                        } else {
                                            $set('propietario_id', null);
                                            $set('propietario_nombre', null);
                                        }
                                    }),

                                TextInput::make('propietario_nombre')
                                    ->label('Propietario Actual')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (TextInput $component, $record) {
                                        if ($record) {
                                            $component->state($record->propietario?->name ?? 'Sin Asignar');
                                        }
                                    }),

                                Hidden::make('propietario_id'),
                            ])->columns(2)
                            ->columnSpan(3),

                        Section::make('Evidencia')
                            ->schema([
                                FileUpload::make('evidencia')
                                    ->label('Foto / Documento')
                                    ->directory('entrega-maletas')
                                    ->image()
                                    ->imageEditor(),
                            ])->columnSpanFull()
                            ->columnSpan(2),
                    ])
                    ->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('maleta.codigo')
                    ->label('Maleta')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('propietario.name')
                    ->label('Propietario')
                    ->searchable(),

                TextColumn::make('responsable.name')
                    ->label('Entregado por')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('detalles_count')
                    ->counts('detalles')
                    ->label('Items')
                    ->badge(),

                TextColumn::make('evidencia')
                    ->label('Evidencia')
                    ->placeholder('No hay')
                    ->formatStateUsing(fn($state) => $state ? 'Ver' : 'No hay')
                    ->icon(fn($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-x-mark')
                    ->color(fn($state) => $state ? 'primary' : 'gray')
                    ->url(fn($record) => $record->evidencia ? Storage::url($record->evidencia) : null)
                    ->openUrlInNewTab()
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetallesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntregaMaletas::route('/'),
            'create' => Pages\CreateEntregaMaleta::route('/create'),
            'edit' => Pages\EditEntregaMaleta::route('/{record}/edit'),
        ];
    }
}