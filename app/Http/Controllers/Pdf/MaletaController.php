<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\ContenidoInforme;
use App\Models\Maleta;
use App\Models\MaletaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;

class MaletaController extends Controller
{
    public function show(Maleta $maleta)
    {
        $maleta->load(['propietario', 'detalles.herramienta']);

        $contenidoInforme = ContenidoInforme::find(1)?->contenido;

        // Obtener todas las herramientas de la maleta
        $herramientas = $maleta->detalles->map(function ($detalle) {
            return $detalle->herramienta->nombre;
        })->filter()->values();

        // Agrupar herramientas por prefijo común
        $herramientasAgrupadas = $this->agruparHerramientas($herramientas);

        // Renderizar el PDF
        $pdf = Pdf::loadView('pdf.maleta', [
            'maleta' => $maleta,
            'generatedAt' => now(),
            'herramientasAgrupadas' => $herramientasAgrupadas,'contenidoInforme' => $contenidoInforme,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("maleta-{$maleta->codigo}.pdf");
    }

    /**
     * Nuevo método para generar PDF con detalles específicos
     */
    public function detallesSeleccionados(Maleta $maleta, string $detalles)
    {
        $detalleIds = explode(',', $detalles);
        
        // Cargar solo los detalles seleccionados
        $detallesSeleccionados = MaletaDetalle::with('herramienta')
            ->whereIn('id', $detalleIds)
            ->where('maleta_id', $maleta->id)
            ->get();
            
        $maleta->load('propietario');
        
        // Obtener las herramientas de los detalles seleccionados
        $herramientas = $detallesSeleccionados->map(function ($detalle) {
            return $detalle->herramienta->nombre;
        })->filter()->values();

        // Agrupar herramientas por prefijo común
        $herramientasAgrupadas = $this->agruparHerramientas($herramientas);

        // Renderizar el PDF
        $pdf = Pdf::loadView('pdf.maleta-detalle', [
            'maleta' => $maleta,
            'generatedAt' => now(),
            'herramientasAgrupadas' => $herramientasAgrupadas,
            'totalHerramientas' => $detallesSeleccionados->count(), // Total de herramientas seleccionadas
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("maleta-{$maleta->codigo}-seleccionadas.pdf");
    }

    /**
     * Agrupa las herramientas por prefijo común
     * 
     * @param \Illuminate\Support\Collection $herramientas
     * @return array
     */
    private function agruparHerramientas($herramientas)
    {
        // Contar ocurrencias de cada herramienta
        $conteo = $herramientas->countBy()->all();
        
        // Agrupar por prefijo común
        $grupos = [];
        $procesadas = [];
        
        foreach ($conteo as $herramienta => $cantidad) {
            if (in_array($herramienta, $procesadas)) {
                continue;
            }
            
            // Buscar otras herramientas con prefijo común
            $prefijo = $this->encontrarPrefijoComunMasLargo($herramienta, array_keys($conteo), $procesadas);
            
            if ($prefijo && strlen($prefijo) > 3) { // Mínimo 3 caracteres para considerar un prefijo válido
                // Agrupar todas las herramientas con este prefijo
                $grupo = [];
                $cantidadTotal = 0;
                
                foreach ($conteo as $h => $c) {
                    if (!in_array($h, $procesadas) && strpos($h, $prefijo) === 0) {
                        $sufijo = trim(substr($h, strlen($prefijo)));
                        
                        // Si no hay sufijo, usar la herramienta completa
                        if (empty($sufijo)) {
                            $sufijo = $h;
                        }
                        
                        // Agregar las veces que aparece
                        for ($i = 0; $i < $c; $i++) {
                            $grupo[] = $sufijo;
                            $cantidadTotal++;
                        }
                        
                        $procesadas[] = $h;
                    }
                }
                
                if (count($grupo) > 0) {
                    $grupos[] = [
                        'prefijo' => trim($prefijo),
                        'cantidad' => $cantidadTotal,
                        'variantes' => $grupo
                    ];
                }
            } else {
                // Si no hay prefijo común significativo, agregar individualmente
                $variantes = [];
                for ($i = 0; $i < $cantidad; $i++) {
                    $variantes[] = '';
                }
                
                $grupos[] = [
                    'prefijo' => $herramienta,
                    'cantidad' => $cantidad,
                    'variantes' => $variantes
                ];
                
                $procesadas[] = $herramienta;
            }
        }
        
        // Ordenar por prefijo alfabéticamente
        usort($grupos, function($a, $b) {
            return strcmp($a['prefijo'], $b['prefijo']);
        });
        
        return collect($grupos);
    }
    
    /**
     * Encuentra el prefijo común más largo entre una herramienta y otras en la lista
     * 
     * @param string $herramienta
     * @param array $todasLasHerramientas
     * @param array $procesadas
     * @return string|null
     */
    private function encontrarPrefijoComunMasLargo($herramienta, $todasLasHerramientas, $procesadas)
    {
        $palabras = explode(' ', $herramienta);
        $mejorPrefijo = null;
        $maxCoincidencias = 0;
        
        // Probar diferentes combinaciones de palabras como prefijo
        for ($longitud = count($palabras); $longitud > 0; $longitud--) {
            $prefijoPrueba = implode(' ', array_slice($palabras, 0, $longitud));
            $coincidencias = 0;
            
            foreach ($todasLasHerramientas as $otraHerramienta) {
                if (!in_array($otraHerramienta, $procesadas) && 
                    $otraHerramienta !== $herramienta &&
                    strpos($otraHerramienta, $prefijoPrueba) === 0) {
                    $coincidencias++;
                }
            }
            
            // Si encontramos al menos una coincidencia, consideramos este prefijo
            if ($coincidencias > 0 && $longitud > $maxCoincidencias) {
                $mejorPrefijo = $prefijoPrueba;
                $maxCoincidencias = $longitud;
                break; // Tomamos el prefijo más largo que tenga coincidencias
            }
        }
        
        // Si no hay otras herramientas con el mismo prefijo, verificar si la herramienta
        // actual tiene un prefijo común consigo misma (aparece múltiples veces)
        if (!$mejorPrefijo) {
            $conteoActual = 0;
            foreach ($todasLasHerramientas as $h) {
                if ($h === $herramienta && !in_array($h, $procesadas)) {
                    $conteoActual++;
                }
            }
            
            // Si aparece múltiples veces, no tiene prefijo común con otras
            if ($conteoActual > 1) {
                return null;
            }
        }
        
        return $mejorPrefijo;
    }
}