<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

// valida la cédula con el algoritmo oficial (módulo 10)
class CedulaEcuatoriana implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cedula = (string) $value;

        if (! preg_match('/^\d{10}$/', $cedula)) {
            $fail('La cédula debe tener exactamente 10 dígitos.');
            return;
        }

        // los 2 primeros dígitos son la provincia (01 a 24) y el tercero debe ser menor a 6
        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24 || (int) $cedula[2] > 5) {
            $fail('La cédula no es válida.');
            return;
        }

        // los dígitos en posición par (0, 2, 4...) se multiplican por 2 y si pasan de 9 se resta 9
        $suma = 0;
        for ($i = 0; $i < 9; $i++) {
            $digito = (int) $cedula[$i];
            if ($i % 2 === 0) {
                $digito *= 2;
                if ($digito > 9) {
                    $digito -= 9;
                }
            }
            $suma += $digito;
        }

        // el último dígito de la cédula tiene que ser igual al verificador
        $verificador = (10 - $suma % 10) % 10;
        if ($verificador !== (int) $cedula[9]) {
            $fail('La cédula no es válida.');
        }
    }
}
