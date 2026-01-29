<?php

namespace App\Livewire\Contabilidad;

use App\Models\Contabilidad;
use App\Models\TrabajoArticulo;
use App\Models\TrabajoOtro;
use Livewire\Component;

class ItemsSorter extends Component
{
    public Contabilidad $record;
    public $items = [];

    public function mount(Contabilidad $record)
    {
        $this->record = $record;
        $this->loadItems();
    }

    public function loadItems()
    {
        // 1. Cargar Artículos CON TODAS SUS RELACIONES
        $articulos = $this->record->trabajoArticulos()
            ->with([
                'articulo',
                'articulo.categoria',
                'articulo.marca',
                'articulo.subCategoria',
                'articulo.presentacion',
                'articulo.unidad'
            ])
            ->get()
            ->map(function ($item) {
                // Aquí usamos el helper para generar el string completo
                $descripcion = $this->buildArticuloLabel($item->articulo);

                // Fallback por si acaso sigue vacío
                if (empty(trim($descripcion))) {
                    $descripcion = "Artículo #" . $item->articulo_id;
                }

                return [
                    'id' => $item->id,
                    'type' => 'articulo',
                    'descripcion' => $descripcion,
                    'precio' => $item->precio,
                    'cantidad' => $item->cantidad,
                    'order' => $item->orden_combinado ?? 9999,
                ];
            });

        // 2. Cargar Otros
        $otros = $this->record->otros()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'otro',
                    'descripcion' => $item->descripcion,
                    'precio' => $item->precio,
                    'cantidad' => $item->cantidad,
                    'order' => $item->orden_combinado ?? 9999,
                ];
            });

        // 3. Fusionar y ordenar
        $this->items = $articulos->concat($otros)
            ->sortBy('order')
            ->values()
            ->toArray();
    }

    public function updateOrder($list)
    {
        foreach ($list as $position => $itemData) {
            // Validamos que el valor tenga el formato correcto antes de explotar
            if (!str_contains($itemData['value'], '-'))
                continue;

            [$type, $id] = explode('-', $itemData['value']);
            $order = $position + 1;

            if ($type === 'articulo') {
                TrabajoArticulo::where('id', $id)->update(['orden_combinado' => $order]);
            } else {
                TrabajoOtro::where('id', $id)->update(['orden_combinado' => $order]);
            }
        }

        // --- SOLUCIÓN: RECARGAR LOS ITEMS ---
        // Esto actualiza la propiedad $items con el nuevo orden de la BD
        // para que Livewire no revierta el cambio visual.
        $this->loadItems();

        \Filament\Notifications\Notification::make()
            ->title('Orden actualizado')
            ->success()
            ->send();
    }

    // Helper copiado de tu RelationManager para mostrar nombre bonito
    private function buildArticuloLabel($articulo): string
    {
        if (!$articulo)
            return 'Artículo no encontrado';

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

    public function render()
    {
        return view('livewire.contabilidad.items-sorter');
    }
}