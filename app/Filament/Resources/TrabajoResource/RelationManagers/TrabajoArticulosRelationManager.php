<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use App\Models\TrabajoArticulo;
use App\Services\FractionService;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
            ->columns([
                TextColumn::make('articulo')
                    ->label('Artículo')
                    ->state(function (TrabajoArticulo $record) {
                        $articulo = $record->articulo;
                        return $this->buildArticuloLabel($articulo);
                    })
                    ->wrap(),
                TextColumn::make('presupuesto')
                    ->label('Presupuesto')
                    ->formatStateUsing(fn($state) => $state ? 'Incluido' : 'Excluido')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->alignCenter(),
                TextColumn::make('precio')
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
