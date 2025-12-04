<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntradaArticuloResource\Pages;
use App\Models\EntradaArticulo;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EntradaArticuloResource extends Resource
{
    protected static ?string $model = EntradaArticulo::class;

    protected static ?string $navigationGroup = 'Logística';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    protected static ?string $modelLabel = 'Entrada de artículo';

    protected static ?string $pluralModelLabel = 'Entradas de artículos';

    protected static ?string $navigationLabel = 'Entradas de artículos';

    public static function form(Form $form): Form
    {
        // No solicitaste formulario de create/edit; lo dejamos vacío.
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // entrada.fecha + entrada.hora -> 29/08/2025 12:30 PM
                TextColumn::make('fecha_hora')
                    ->label('Fecha')
                    ->state(function (EntradaArticulo $record) {
                        $fecha = optional($record->entrada?->fecha)->format('d/m/Y');
                        // 'hora' está casteado a datetime en el modelo Entrada
                        $hora = optional($record->entrada?->hora)->format('h:i A');
                        return trim(($fecha ?? '') . ' ' . ($hora ?? ''));
                    })
                    ->sortable(query: function (Builder $query, string $direction) {
                        // Ordenar por fecha y hora de la entrada
                        $query
                            ->join('entradas', 'entrada_articulos.entrada_id', '=', 'entradas.id')
                            ->orderBy('entradas.fecha', $direction)
                            ->orderBy('entradas.hora', $direction)
                            ->select('entrada_articulos.*'); // evitar columnas ambiguas
                    })
                    ->toggleable(),

                // entrada.codigo (ajusta a entrada.guia si corresponde)
                TextColumn::make('entrada.guia')
                    ->label('Guía')
                    ->sortable()
                    ->searchable(isIndividual: true),

                // Descripción completa del artículo con buscador individual personalizado
                TextColumn::make('articulo_descripcion')
                    ->label('Descripción del artículo')
                    ->state(function (EntradaArticulo $record) {
                        $articulo = $record->articulo;

                        $categoria = $articulo?->categoria?->nombre ?? null;
                        $marca = $articulo?->marca?->nombre ?? null;
                        $subCategoria = $articulo?->subCategoria?->nombre ?? null;
                        $especificacion = $articulo?->especificacion ?? null;
                        $presentacion = $articulo?->presentacion?->nombre ?? null;
                        $medida = $articulo?->medida ?? null;
                        $unidad = $articulo?->unidad?->nombre ?? null;
                        $color = $articulo?->color ?? null;

                        $parts = array_filter([
                            $categoria,
                            $marca,
                            $subCategoria,
                            $especificacion,
                            $presentacion,
                            $medida,
                            $unidad,
                            $color,
                        ]);

                        return implode(' ', $parts);
                    })
                    ->searchable(isIndividual: true, query: function (Builder $query, string $search): Builder {
                        // Buscar en todos los campos/relaciones mencionados
                        $like = '%' . str($search)->lower() . '%';

                        return $query->where(function (Builder $q) use ($like) {
                            $q->whereHas('articulo', function (Builder $qa) use ($like) {
                                $qa->whereRaw('LOWER(especificacion) LIKE ?', [$like])
                                    ->orWhereRaw('LOWER(medida) LIKE ?', [$like])
                                    ->orWhereRaw('LOWER(color) LIKE ?', [$like])
                                    // Relaciones nominales
                                    ->orWhereHas('categoria', function (Builder $r) use ($like) {
                                        $r->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                                    })
                                    ->orWhereHas('marca', function (Builder $r) use ($like) {
                                        $r->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                                    })
                                    ->orWhereHas('subCategoria', function (Builder $r) use ($like) {
                                        $r->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                                    })
                                    ->orWhereHas('presentacion', function (Builder $r) use ($like) {
                                        $r->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                                    })
                                    ->orWhereHas('unidad', function (Builder $r) use ($like) {
                                        $r->whereRaw('LOWER(nombre) LIKE ?', [$like]);
                                    });
                            });
                        });
                    })
                    ->wrap() // por si la descripción es larga
                    ->toggleable(),

                // costo (formato 20.00)
                TextColumn::make('costo')
                    ->label('Costo')
                    ->alignRight()
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2, '.', '')),

                // cantidad
                TextColumn::make('cantidad')
                    ->label('Cant.')
                    ->alignCenter()
                    ->sortable(),

                // subtotal = costo * cantidad
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->alignRight()
                    ->state(fn(EntradaArticulo $record) => (float) $record->costo * (float) $record->cantidad)
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2, '.', ''))
                    ->sortable(query: function (Builder $query, string $direction) {
                        // Ordenar por subtotal calculado
                        $query->orderByRaw('(costo * cantidad) ' . $direction);
                    }),

                // entrada.codigo (ajusta a entrada.guia si corresponde)
                TextColumn::make('entrada.responsable.name')
                    ->sortable()
                    ->searchable(isIndividual: true),

                TextColumn::make('entrada.proveedor.nombre')
                    ->placeholder('Sin proveedor')
                    ->sortable()
                    ->searchable(isIndividual: true),

                // entrada.evidencia_url como link a nueva pestaña
                TextColumn::make('evidencia')
                    ->label('Evidencia')
                    ->state(fn(EntradaArticulo $record) => $record->entrada?->evidencia_url ? 'Ver' : '—')
                    ->url(function (EntradaArticulo $record) {
                        $raw = $record->entrada?->evidencia_url;

                        if (!$raw) {
                            return null;
                        }

                        // Si ya viene una URL absoluta (http/https), úsala tal cual.
                        if (preg_match('#^https?://#i', $raw)) {
                            return $raw;
                        }

                        // Normaliza cualquier forma guardada (p.ej. "entrada/xxx.jpg" o "public/entrada/xxx.jpg")
                        $filename = basename($raw);

                        // Genera URL absoluta tipo: /storage/entrada/xxx.jpg
                        return Storage::url('entrada/' . $filename);
                    }, shouldOpenInNewTab: true)
                    ->color(fn(EntradaArticulo $record) => $record->entrada?->evidencia_url ? 'primary' : 'gray')
                    ->icon(fn(EntradaArticulo $record) => $record->entrada?->evidencia_url ? 'heroicon-o-arrow-top-right-on-square' : null),
            ])
            ->defaultSort('id', 'desc') // fallback de orden
            ->filters([
                // Puedes agregar filtros por rango de fechas si lo necesitas más adelante
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
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
            'index' => Pages\ListEntradaArticulos::route('/'),
            // 'create' => Pages\CreateEntradaArticulo::route('/create'),
            // 'edit' => Pages\EditEntradaArticulo::route('/{record}/edit'),
        ];
    }
}
