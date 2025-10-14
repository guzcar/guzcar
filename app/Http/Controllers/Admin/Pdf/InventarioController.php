<?php

namespace App\Http\Controllers\Admin\Pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function ingreso(Trabajo $trabajo) {
        
        $codigo = $trabajo->codigo;
        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();
        $vehiculo = $trabajo->vehiculo;

        $pdf = Pdf::loadView('admin.pdf.ingreso', compact('codigo', 'clientePrincipal', 'vehiculo', 'trabajo'));
        
        $filename = "checklist_ingreso.pdf";
        
        return $pdf->stream($filename);
    }
}