<?php

namespace App\Http\Controllers;

// Importamos todos los modelos y facades necesarios
use App\Models\Presupuesto;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; 
use NumberToWords\NumberToWords;

class TestPresupuestoController extends Controller
{

    private function validarPresupuesto(Request $request)
    {
        return $request->validate([
            // --- AJUSTE AQUÍ ---
            'cliente_id' => 'nullable|exists:clientes,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            // --- FIN AJUSTE ---
            'observacion' => 'nullable|string',
            'servicios' => 'nullable|array',
            'servicios.*.descripcion' => 'required|string|max:255',
            'servicios.*.cantidad' => 'required|integer|min:1',
            'servicios.*.precio' => 'required|numeric|min:0',
            'articulos' => 'nullable|array',
            'articulos.*.descripcion' => 'required|string|max:255',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.precio' => 'required|numeric|min:0',
        ]);
    }

    /**
     * Muestra la lista de presupuestos.
     */
    public function index()
    {
        // Cargamos las relaciones necesarias para la vista
        // vehiculo.marca es la relación anidada que pediste
        $presupuestos = Presupuesto::with('cliente', 'vehiculo.marca')
            ->latest() // Opcional: mostrar los más nuevos primero
            ->paginate(15); // Paginación

        return view('test.presupuestos.index', compact('presupuestos'));
    }

    /**
     * Muestra el formulario para crear uno nuevo.
     */
    public function create()
    {
        // Pasamos un presupuesto vacío para unificar el formulario
        $presupuesto = new Presupuesto();
        return view('test.presupuestos.form', compact('presupuesto'));
    }

    /**
     * Guarda el nuevo presupuesto en la BD.
     */
    public function store(Request $request)
    {
        $this->validarPresupuesto($request); // Usamos la validación

        try {
            DB::beginTransaction();

            $presupuesto = Presupuesto::create($request->only('cliente_id', 'vehiculo_id', 'observacion'));

            if ($request->filled('servicios')) {
                $presupuesto->servicios()->createMany($request->servicios);
            }
            if ($request->filled('articulos')) {
                $presupuesto->articulos()->createMany($request->articulos);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }

        return redirect()->route('test.presupuestos.index')->with('success', 'Presupuesto creado.');
    }

    /**
     * Muestra el formulario para editar.
     */
    public function edit(Presupuesto $presupuesto)
    {
        // Cargamos todas las relaciones para el formulario
        $presupuesto->load('cliente', 'vehiculo.marca', 'servicios', 'articulos');
        return view('test.presupuestos.form', compact('presupuesto'));
    }

    /**
     * Actualiza el presupuesto en la BD.
     */
    public function update(Request $request, Presupuesto $presupuesto)
    {
        $this->validarPresupuesto($request); // Reutilizamos la validación

        try {
            DB::beginTransaction();

            $presupuesto->update($request->only('cliente_id', 'vehiculo_id', 'observacion'));

            // Sincronizar detalles (Borrar y crear es lo más simple)
            $presupuesto->servicios()->delete();
            if ($request->filled('servicios')) {
                $presupuesto->servicios()->createMany($request->servicios);
            }

            $presupuesto->articulos()->delete();
            if ($request->filled('articulos')) {
                $presupuesto->articulos()->createMany($request->articulos);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }

        return redirect()->route('test.presupuestos.index')->with('success', 'Presupuesto actualizado.');
    }

    /**
     * Borra un presupuesto.
     */
    public function destroy(Presupuesto $presupuesto)
    {
        // Gracias a cascadeOnDelete en las migraciones,
        // al borrar el presupuesto se borrarán sus servicios y artículos.
        $presupuesto->delete();
        return redirect()->route('test.presupuestos.index')->with('success', 'Presupuesto eliminado.');
    }

    // --- MÉTODOS ESPECIALES ---

    /**
     * Genera y muestra el PDF.
     */
    public function printPdf(Presupuesto $presupuesto)
    {
        // Cargar todas las relaciones necesarias
        $presupuesto->load(
            'cliente',
            'vehiculo.marca',
            'vehiculo.modelo',
            'vehiculo.tipoVehiculo',
            'servicios',
            'articulos'
        );

        // 1. Cálculos de subtotales
        $subtotal_servicios = $presupuesto->servicios->sum(fn($i) => $i->cantidad * $i->precio);
        $subtotal_articulos = $presupuesto->articulos->sum(fn($i) => $i->cantidad * $i->precio);

        // $total en tu template es el subtotal general
        $total = $subtotal_servicios + $subtotal_articulos;

        // 2. Descuentos (No los tenemos, así que los simulamos)
        $descuentos = collect(); // Colección vacía
        $total_con_descuentos = $total; // Es el mismo total

        // 3. IGV y Total Final
        $monto_igv = 0;
        $total_con_igv = $total_con_descuentos;

        if ($presupuesto->igv) { // Si el booleano es true
            $monto_igv = $total_con_descuentos * 0.18;
            $total_con_igv = $total_con_descuentos + $monto_igv;
        }

        // 4. Convertir a palabras
        $palabras = 'Error al convertir a palabras. Verifique la instalación de kwn/number-to-words.';
        try {
            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('es');
            $entero = floor($total_con_igv);
            $decimal = round(($total_con_igv - $entero) * 100);
            $palabras = strtoupper($numberTransformer->toWords($entero)) . " CON $decimal/100 SOLES";
        } catch (\Exception $e) {
            // Error logged implícitamente, $palabras ya tiene el msg de error
        }

        // 5. Preparar datos para la vista
        $data = [
            'presupuesto' => $presupuesto,
            'subtotal_servicios' => $subtotal_servicios,
            'subtotal_articulos' => $subtotal_articulos,
            'total' => $total,
            'descuentos' => $descuentos,
            'total_con_descuentos' => $total_con_descuentos,
            'monto_igv' => $monto_igv,
            'total_con_igv' => $total_con_igv,
            'palabras' => $palabras,
        ];

        $pdf = Pdf::loadView('test.presupuestos.pdf', $data);
        return $pdf->stream('presupuesto-' . $presupuesto->id . '.pdf');
    }

    // --- MÉTODOS DE BÚSQUEDA (API) ---

    /**
     * Busca Clientes para el select.
     */
    public function searchClientes(Request $request)
    {
        $term = $request->query('term', '');

        if (strlen($term) < 2) { // No buscar si el término es muy corto
            return response()->json([]);
        }

        $clientes = Cliente::where('nombre', 'LIKE', "%{$term}%")
            ->orWhere('identificador', 'LIKE', "%{$term}%")
            ->select('id', 'nombre', 'identificador')
            ->limit(20) // Límite para no sobrecargar
            ->get()
            ->map(fn($c) => [ // Formato para Select2
                'id' => $c->id,
                'text' => "{$c->nombre} ({$c->identificador})"
            ]);

        return response()->json($clientes);
    }

    /**
     * Busca Vehículos para el select.
     */
    public function searchVehiculos(Request $request)
    {
        $term = $request->query('term', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        // --- 1. Cargamos ambas relaciones (marca y modelo) ---
        $vehiculos = Vehiculo::with('marca', 'modelo')
            // Agrupamos todos los OR en un "where" para lógica correcta
            ->where(function ($query) use ($term) {

                // Búsqueda en la tabla 'vehiculos'
                $query->where('placa', 'LIKE', "%{$term}%")
                    ->orWhere('vin', 'LIKE', "%{$term}%")
                    ->orWhere('motor', 'LIKE', "%{$term}%")
                    // --- NUEVO: Búsqueda por color ---
                    ->orWhere('color', 'LIKE', "%{$term}%");

                // Búsqueda en la relación 'marca'
                $query->orWhereHas('marca', function ($subQuery) use ($term) {
                    $subQuery->where('nombre', 'LIKE', "%{$term}%");
                });

                // --- NUEVO: Búsqueda por modelo ---
                $query->orWhereHas('modelo', function ($subQuery) use ($term) {
                    // Asumo que la columna en 'vehiculo_modelos' es 'nombre'
                    $subQuery->where('nombre', 'LIKE', "%{$term}%");
                });
            })
            ->limit(20) // Mantenemos el límite
            ->get()
            // --- 2. Mejoramos el texto de respuesta ---
            ->map(fn($v) => [
                'id' => $v->id,
                'text' => "{$v->placa} - {$v->marca?->nombre} {$v->modelo?->nombre} ({$v->color})"
            ]);

        return response()->json($vehiculos);
    }
}