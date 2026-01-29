<?php

namespace App\Filament\Resources\EquipoResource\RelationManagers;

use App\Models\Implemento;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
// Asegúrate de tener instalado el plugin de Excel si usas ExportBulkAction, si no, comenta esa línea
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction; 

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $modelLabel = 'Implemento';

    protected static ?string $title = 'Implementos Asignados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('implemento_id')
                                    ->label('Implemento')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    // Opción para crear nuevo Implemento in-situ
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->unique('implementos', 'nombre'),
                                        Hidden::make('stock')->default(1),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return Implemento::create($data)->id;
                                    })
                                    ->live()
                                    // Al cambiar, cargar stock
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $stock = Implemento::find($state)?->stock ?? 0;
                                            $set('stock_live', $stock);
                                        } else {
                                            $set('stock_live', 0);
                                        }
                                    })
                                    ->relationship('implemento', 'nombre')
                                    ->disabledOn('edit') // Bloqueado en edición por lógica de triggers
                                    ->columnSpan(1),

                                TextInput::make('stock_live')
                                    ->label('Stock Actual')
                                    ->numeric()
                                    ->minValue(0)
                                    ->disabled(fn(Get $get) => !$get('implemento_id'))
                                    ->dehydrated(false) // No guardar en pivot
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $impId = $get('implemento_id');
                                        if ($impId && $state !== null) {
                                            $imp = Implemento::find($impId);
                                            if ($imp) {
                                                $imp->update(['stock' => $state]);
                                                Notification::make()
                                                    ->title('Stock actualizado')
                                                    ->success()
                                                    ->duration(1000)
                                                    ->send();
                                            }
                                        }
                                    })
                                    ->prefixIcon(fn($state) => $state > 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                                    ->prefixIconColor(fn($state) => $state > 0 ? 'success' : 'danger')
                                    ->columnSpan(1),

                                // Campo auxiliar
                                Hidden::make('stock_trigger_refresh'),

                                TextInput::make('ultimo_estado')
                                    ->disabled()
                                    ->hiddenOn('create'),
                            ])
                            ->columns(1)
                            ->columnSpan(1),

                        FileUpload::make('evidencia_url')
                            ->label('Evidencia')
                            ->directory('equipo_detalles_evidencia')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('implemento.nombre')
                    ->label('Implemento')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                ImageColumn::make('evidencia_url')
                    ->placeholder('Sin evidencia')
                    ->label('Evidencia')
                    ->width(70)
                    ->height(70),
                TextColumn::make('ultimo_estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'OPERATIVO' => 'success',
                        'MERMA' => 'warning',
                        'PERDIDO' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Asignar Implemento'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->button(),
                Tables\Actions\Action::make('viewImage')
                    ->button()
                    ->label('Ver')
                    ->icon('heroicon-s-eye')
                    ->color('gray')
                    ->disabled(fn($record) => empty($record->evidencia_url))
                    ->url(fn($record) => Storage::url($record->evidencia_url))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->label('Quitar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Si usas el plugin de excel:
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}