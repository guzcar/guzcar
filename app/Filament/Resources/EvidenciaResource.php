<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvidenciaResource\Pages;
use App\Filament\Resources\EvidenciaResource\RelationManagers;
use App\Models\Evidencia;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EvidenciaResource extends Resource
{
    protected static ?string $model = Evidencia::class;

    protected static ?string $navigationGroup = 'Histórico';

    protected static ?int $navigationSort = 90;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Evidencias subidas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('trabajo_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                FileUpload::make('evidencia_url')
                    ->directory('evidencia')
                    ->required(),
                Forms\Components\TextInput::make('tipo')
                    ->required(),
                Forms\Components\Textarea::make('observacion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make('Trabajo', [
                    TextColumn::make('trabajo.codigo')
                        ->label('Código')
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.fecha_ingreso')
                        ->label('Fecha de Ingreso')
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.fecha_salida')
                        ->label('Fecha de Salida')
                        ->placeholder('Sin Salida')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Vehiculo', [
                    TextColumn::make('trabajo.vehiculo.placa')
                        ->label('Placa')
                        ->placeholder('Sin Placa')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.marca')
                        ->label('Marca')
                        ->sortable()
                        ->searchable(isIndividual: true),
                    TextColumn::make('trabajo.vehiculo.modelo')
                        ->label('Modelo')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.vehiculo.color')
                        ->label('Color')
                        ->sortable()
                        ->searchable(isIndividual: true)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('trabajo.vehiculo.clientes.nombre')
                        ->placeholder('Sin Clientes')
                        ->searchable(isIndividual: true)
                        ->badge()
                        ->wrap()
                        ->toggleable(isToggledHiddenByDefault: true)
                ]),
                ColumnGroup::make('Evidencia', [
                    ImageColumn::make('evidencia_url')
                        ->size(40)
                        ->label('Archivo')
                        ->getStateUsing(function (Evidencia $record): string {
                            if ($record->tipo === 'imagen') {
                                return $record->evidencia_url;
                            }
                            return asset('images/video.png');
                        })
                        ->alignCenter()
                        ->verticallyAlignCenter(),
                    TextColumn::make('user.name')
                        ->label('Subido por')
                        ->searchable(),
                    TextColumn::make('observacion')
                        ->label('Observación')
                        ->wrap()
                        ->lineClamp(3)
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
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
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make()
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
            'index' => Pages\ListEvidencias::route('/'),
            // 'create' => Pages\CreateEvidencia::route('/create'),
            'view' => Pages\ViewEvidencia::route('/{record}'),
            // 'edit' => Pages\EditEvidencia::route('/{record}/edit'),
        ];
    }
}
