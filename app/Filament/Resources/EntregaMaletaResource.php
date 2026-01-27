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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
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
                                    ->label('Evidencias (Fotos/Documentos)')
                                    ->directory('entrega-maletas')
                                    ->multiple()
                                    ->reorderable()
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpanFull(),
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

                Tables\Columns\ImageColumn::make('evidencia')
                    ->label('Evidencias')
                    ->placeholder('Sin Evidencias')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->size(40),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Ver'),
                    Tables\Actions\Action::make('pdf')
                        ->label('Acta de entrega')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record) => route('pdf.entrega.acta', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make(),
                ])->button(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Detalles de la Entrega')
                    ->schema([
                        InfoGrid::make(3)->schema([
                            TextEntry::make('fecha')->dateTime('d/m/Y H:i')->label('Fecha'),
                            TextEntry::make('maleta.codigo')->label('Maleta'),
                            TextEntry::make('propietario.name')->label('Propietario'),
                            TextEntry::make('responsable.name')->label('Responsable'),
                        ]),
                    ]),

                InfoSection::make('Archivos de Evidencia')
                    ->schema([
                        // Opción A: Si son SOLO IMÁGENES
                        ImageEntry::make('evidencia')
                            ->label('Galería Fotográfica')
                            ->hiddenLabel()
                            ->columns(4) // Muestra 4 por fila
                            ->height(150),

                        // Opción B: Si mezclas FOTOS y PDFs (Muestra una lista descargable)
                        /* TextEntry::make('evidencia')
                            ->label('Archivos Adjuntos')
                            ->listWithLineBreaks()
                            ->html()
                            ->formatStateUsing(function ($state) {
                                // Lógica para crear links si es necesario, 
                                // pero ImageEntry suele ser suficiente para visuales.
                                return $state; 
                            }),
                        */
                    ])
                    ->collapsible(),
            ]);
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