<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EspecialidadController extends Controller
{
    // READ
    public function index()
    {
        return Especialidad::orderBy('nombre')->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255|unique:especialidades',
        ]);

        return Especialidad::create($datos);
    }

    // UPDATE
    public function update(Request $request, Especialidad $especialidad)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('especialidades')->ignore($especialidad->id)],
        ]);

        $especialidad->update($datos);

        return $especialidad;
    }

    // DELETE: no se borra si tiene médicos
    public function destroy(Especialidad $especialidad)
    {
        if ($especialidad->medicos()->exists()) {
            return response()->json(['message' => 'No se puede eliminar: hay médicos con esta especialidad'], 409);
        }

        $especialidad->delete();

        return response()->json(['mensaje' => 'Especialidad eliminada']);
    }
}
