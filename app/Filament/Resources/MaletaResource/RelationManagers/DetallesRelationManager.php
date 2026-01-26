<?php

namespace App\Filament\Resources\MaletaResource\RelationManagers;

use App\Models\Herramienta;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Notifications\Notification;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $modelLabel = 'Herramienta';

    protected static ?string $title = 'Herramientas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('herramienta_id')
                                    ->label('Herramienta')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    // Opción para crear nueva (Stock inicial 1 por defecto)
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->unique('herramientas', 'nombre'),
                                        Hidden::make('stock')->default(1),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        // Lógica manual de creación para asegurar retorno de ID
                                        return Herramienta::create($data)->id;
                                    })
                                    // Reactividad esencial
                                    ->live()
                                    // Cuando cambias la herramienta, cargamos su stock en el campo de abajo
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $stock = Herramienta::find($state)?->stock ?? 0;
                                            $set('stock_live', $stock);
                                        } else {
                                            $set('stock_live', 0);
                                        }
                                    })
                                    ->relationship('herramienta', 'nombre')
                                    ->disabledOn('edit') // Bloqueado en edición por lógica de triggers
                                    ->columnSpan(1),

                                TextInput::make('stock_live')
                                    ->label('Stock Actual')
                                    ->numeric()
                                    ->minValue(0)
                                    // Solo habilitado si hay herramienta seleccionada
                                    ->disabled(fn(Get $get) => !$get('herramienta_id'))
                                    // IMPORTANTE: Esto evita que Filament intente guardar 'stock_live' en 'maleta_detalles'
                                    ->dehydrated(false)
                                    // IMPORTANTE: Ejecuta la acción al salir del campo o pausar escritura
                                    ->live(onBlur: true)
                                    // Lógica de actualización directa a BD
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        $herramientaId = $get('herramienta_id');

                                        if ($herramientaId && $state !== null) {
                                            $herramienta = Herramienta::find($herramientaId);

                                            if ($herramienta) {
                                                // Actualizamos la BD real
                                                $herramienta->update(['stock' => $state]);

                                                // Notificación visual (opcional pero recomendada)
                                                Notification::make()
                                                    ->title('Stock actualizado en BD')
                                                    ->success()
                                                    ->duration(1000)
                                                    ->send();
                                            }
                                        }
                                    })
                                    // Validación visual para que el usuario sepa si alcanzará
                                    ->prefixIcon(fn($state) => $state > 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                                    ->prefixIconColor(fn($state) => $state > 0 ? 'success' : 'danger')
                                    ->columnSpan(1),

                                // Campo auxiliar invisible para forzar reactividad
                                Hidden::make('stock_trigger_refresh'),

                                TextInput::make('ultimo_estado')
                                    ->disabled()
                                    ->hiddenOn('create'),
                            ])
                            ->columns(1)
                            ->columnSpan(1),

                        FileUpload::make('evidencia_url')
                            ->label('Evidencia')
                            ->directory('maleta_detalles')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ... (Tu configuración de tabla se mantiene igual)
            ->defaultSort('created_at', 'desc')
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('herramienta.nombre')
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
                    ->label('Agregar herramienta'),
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
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}