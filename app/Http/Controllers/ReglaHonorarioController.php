<?php

namespace App\Http\Controllers;

use App\Models\ReglaHonorario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReglaHonorarioController extends Controller
{
    // READ
    public function index()
    {
        return ReglaHonorario::orderBy('umbral')->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate([
            'umbral' => 'required|numeric|min:0|unique:reglas_honorarios',
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        return ReglaHonorario::create($datos);
    }

    // UPDATE: la regla de umbral 0 no puede cambiar de umbral
    public function update(Request $request, ReglaHonorario $regla)
    {
        $datos = $request->validate([
            'umbral' => ['required', 'numeric', 'min:0', Rule::unique('reglas_honorarios')->ignore($regla->id)],
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        if ($regla->umbral == 0 && (float) $datos['umbral'] !== 0.0) {
            return response()->json(['message' => 'La regla con umbral 0 es obligatoria y no puede cambiar su umbral'], 422);
        }

        $regla->update($datos);

        return $regla;
    }

    // DELETE: siempre tiene que existir una regla con umbral 0
    public function destroy(ReglaHonorario $regla)
    {
        if ($regla->umbral == 0) {
            return response()->json(['message' => 'No se puede eliminar la regla con umbral 0'], 409);
        }

        $regla->delete();

        return response()->json(['mensaje' => 'Regla eliminada']);
    }
}
