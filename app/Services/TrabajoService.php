<?php

namespace App\Services;

use App\Models\Trabajo;

class TrabajoService
{

    public static function actualizarTrabajoPorId(?Trabajo $record)
    {

        if ($record) {
            if ($record->fecha_salida) {
                $importe = $record->importe;
                $aCuenta = $record->a_cuenta;

                if ($aCuenta == 0) {
                    $desembolso = 'POR COBRAR';
                } elseif ($aCuenta >= $importe) {
                    $desembolso = 'COBRADO';
                } elseif ($aCuenta < $importe) {
                    $desembolso = 'A CUENTA';
                } else {
                    $desembolso = 'POR COBRAR';
                }
                $record->update([
                    'desembolso' => $desembolso,
                ]);
            }
        }
    }
}
