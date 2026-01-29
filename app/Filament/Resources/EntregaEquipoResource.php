<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntregaEquipoResource\Pages;
use App\Filament\Resources\EntregaEquipoResource\RelationManagers;
use App\Models\EntregaEquipo;
use App\Models\Equipo;
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

class EntregaEquipoResource extends Resource
{
    protected static ?string $model = EntregaEquipo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Equipos e Implementos';
    protected static ?string $navigationLabel = 'Entregas de Equipo';
    protected static ?int $navigationSort = 65;

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

                                Select::make('equipo_id')
                                    ->disabledOn('edit')
                                    ->label('Equipo')
                                    ->relationship('equipo', 'codigo')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (?int $state, Set $set) {
                                        if ($state) {
                                            $equipo = Equipo::with('propietario')->find($state);
                                            $set('propietario_id', $equipo?->propietario_id);
                                            $set('propietario_nombre', $equipo?->propietario?->name ?? 'Sin Asignar');
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
                                    ->directory('entrega-equipos') // Directorio actualizado
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

                TextColumn::make('equipo.codigo')
                    ->label('Equipo')
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
                        ->url(fn($record) => route('pdf.entrega_equipo.acta', $record)) // Ruta actualizada
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
                            TextEntry::make('equipo.codigo')->label('Equipo'),
                            TextEntry::make('propietario.name')->label('Propietario'),
                            TextEntry::make('responsable.name')->label('Responsable'),
                        ]),
                    ]),

                InfoSection::make('Archivos de Evidencia')
                    ->schema([
                        ImageEntry::make('evidencia')
                            ->label('Galería Fotográfica')
                            ->hiddenLabel()
                            ->columns(4)
                            ->height(150),
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
            'index' => Pages\ListEntregaEquipos::route('/'),
            'create' => Pages\CreateEntregaEquipo::route('/create'),
            'edit' => Pages\EditEntregaEquipo::route('/{record}/edit'),
        ];
    }
}