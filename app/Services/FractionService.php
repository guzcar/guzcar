<?php

namespace App\Services;

class FractionService
{
    private const TOLERANCE = 1.e-6;
    private const MAX_DENOMINATOR = 1000;

    public function decimalToFraction(float $decimal): string
    {
        // Extraer la parte entera
        $integerPart = (int) $decimal;
        $decimalPart = $decimal - $integerPart;

        // Si la parte decimal es muy pequeña, devolver solo la parte entera
        if (abs($decimalPart) < self::TOLERANCE) {
            return (string) $integerPart;
        }

        // Convertir la parte decimal a fracción
        $bestError = PHP_FLOAT_MAX;
        $bestNumerator = 1;
        $bestDenominator = 1;

        for ($denominator = 1; $denominator <= self::MAX_DENOMINATOR; $denominator++) {
            $numerator = round($decimalPart * $denominator);
            $error = abs($decimalPart - ($numerator / $denominator));

            if ($error < $bestError) {
                $bestError = $error;
                $bestNumerator = $numerator;
                $bestDenominator = $denominator;
            }
        }

        // Simplificar la fracción
        $gcd = \gmp_gcd($bestNumerator, $bestDenominator);
        $bestNumerator /= \gmp_intval($gcd);
        $bestDenominator /= \gmp_intval($gcd);

        // Si el denominador es 1, devolver solo el numerador
        if ($bestDenominator == 1) {
            return $integerPart ? "$integerPart $bestNumerator" : "$bestNumerator";
        } else {
            return $integerPart ? "$integerPart $bestNumerator/$bestDenominator" : "$bestNumerator/$bestDenominator";
        }
    }
}
