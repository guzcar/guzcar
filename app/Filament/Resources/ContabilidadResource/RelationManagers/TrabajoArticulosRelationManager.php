<?php

namespace App\Filament\Resources\ContabilidadResource\RelationManagers;

use App\Models\TrabajoArticulo;
use App\Services\FractionService;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TrabajoArticulosRelationManager extends RelationManager
{
    protected static string $relationship = 'trabajoArticulos';

    protected static ?string $title = 'Artículos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('articulo_nombre')
                    ->label('Artículo')
                    ->disabled()
                    ->columnSpanFull()
                    ->afterStateHydrated(function (TextInput $component, $record) {
                        $articulo = $record->articulo;
                        $label = $this->buildArticuloLabel($articulo);
                        $component->state($label);
                    }),
                TextInput::make('cantidad_fraccion')
                    ->label('Cantidad')
                    ->disabled() // Hace que el campo sea de solo lectura
                    ->afterStateHydrated(function (TextInput $component, $record) {
                        $cantidadFormateada = FractionService::decimalToFraction((float) $record->cantidad);
                        $component->state($cantidadFormateada);
                    }),
                TextInput::make('precio')
                    ->label('Precio para el servicio')
                    ->required()
                    ->numeric()
                    ->prefix('S/ ')
                    ->maxValue(42949672.95)
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort', 'asc')
            ->columns([
                TextColumn::make('articulo')
                    ->label('Artículo')
                    ->state(function (TrabajoArticulo $record) {
                        $articulo = $record->articulo;
                        return $this->buildArticuloLabel($articulo);
                    })
                    ->wrap(),
                TextColumn::make('tecnico.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('responsable.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('presupuesto')
                    ->label('Presupuesto')
                    ->formatStateUsing(fn($state) => $state ? 'Incluido' : 'Excluido')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->alignCenter(),
                // Columna para mostrar el costo original del artículo
                TextColumn::make('articulo.costo')
                    ->label('Costo')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->sortable()
                    ->state(function (TrabajoArticulo $record) {
                        return number_format($record->articulo->costo, 2, '.', '');
                    }),
                TextColumn::make('precio') // Este se actualiza según el porcentaje de margen
                    ->extraAttributes(['class' => 'bg-gray-100 dark:bg-gray-700'])
                    ->label('Precio')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('cantidad')
                    ->alignCenter()
                    ->formatStateUsing(function ($state) {
                        return FractionService::decimalToFraction((float) $state);
                    }),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('S/ ')
                    ->alignRight()
                    ->state(function (TrabajoArticulo $record): string {
                        return number_format($record->precio * $record->cantidad, 2, '.', '');
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([

                // Action para aplicar margen de ganancia
                Tables\Actions\Action::make('aplicarMargenGanancia')
                    ->modalWidth(MaxWidth::Medium)
                    ->label('Margen de Ganancia')
                    ->icon('heroicon-o-calculator')
                    ->form([
                        TextInput::make('porcentaje_margen')
                            ->label('Porcentaje de Margen de Ganancia')
                            ->default(30)
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                    ])
                    ->action(function (array $data) {
                        $porcentaje = $data['porcentaje_margen'];

                        // Obtener todos los artículos del trabajo actual
                        $trabajoArticulos = $this->getOwnerRecord()->trabajoArticulos;

                        foreach ($trabajoArticulos as $trabajoArticulo) {
                            // Calcular el nuevo precio basado en el costo y el margen
                            $costo = $trabajoArticulo->articulo->costo;
                            $nuevoPrecio = $costo * (1 + ($porcentaje / 100));

                            // Redondear a número entero
                            $nuevoPrecioEntero = round($nuevoPrecio);

                            // Actualizar el precio del artículo en el trabajo
                            $trabajoArticulo->precio = $nuevoPrecioEntero;
                            $trabajoArticulo->save();
                        }

                        // Mostrar mensaje de éxito
                        \Filament\Notifications\Notification::make()
                            ->title('Margen aplicado exitosamente')
                            ->body("Se aplicó un margen de {$porcentaje}% a todos los artículos (precios redondeados)")
                            ->success()
                            ->send();
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                BulkActionGroup::make([
                    BulkAction::make('marcarComoSi')
                        ->label('Incluir en el Presupuesto')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->presupuesto = true; // Cambiar a "SI"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('marcarComoNo')
                        ->label('Excluir del Presupuesto')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->presupuesto = false; // Cambiar a "NO"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    /**
     * Construye el label del artículo dinámicamente.
     */
    private function buildArticuloLabel($articulo): string
    {
        $categoria = $articulo->categoria->nombre ?? null;
        $marca = $articulo->marca->nombre ?? null;
        $subCategoria = $articulo->subCategoria->nombre ?? null;
        $especificacion = $articulo->especificacion ?? null;
        $presentacion = $articulo->presentacion->nombre ?? null;
        $medida = $articulo->medida ?? null;
        $unidad = $articulo->unidad->nombre ?? null;
        $color = $articulo->color ?? null;

        // Construye el label dinámicamente
        $labelParts = [];
        if ($categoria)
            $labelParts[] = $categoria;
        if ($marca)
            $labelParts[] = $marca;
        if ($subCategoria)
            $labelParts[] = $subCategoria;
        if ($especificacion)
            $labelParts[] = $especificacion;
        if ($presentacion)
            $labelParts[] = $presentacion;
        if ($medida)
            $labelParts[] = $medida;
        if ($unidad)
            $labelParts[] = $unidad;
        if ($color)
            $labelParts[] = $color;

        // Une las partes con un espacio
        return implode(' ', $labelParts);
    }
}