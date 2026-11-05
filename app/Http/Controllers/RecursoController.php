<?php

namespace App\Http\Controllers;

use App\Models\Recurso;
use Illuminate\Http\Request;

class RecursoController extends Controller
{
    // READ
    public function index()
    {
        return Recurso::orderBy('nombre')->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'tarifa_hora' => 'required|numeric|min:0.01',
        ]);

        return Recurso::create($datos);
    }

    // UPDATE
    public function update(Request $request, Recurso $recurso)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'tarifa_hora' => 'required|numeric|min:0.01',
        ]);

        $recurso->update($datos);

        return $recurso;
    }

    // DELETE: no se borra si ya tiene usos registrados
    public function destroy(Recurso $recurso)
    {
        if ($recurso->usos()->exists()) {
            return response()->json(['message' => 'No se puede eliminar: el recurso ya tiene usos registrados'], 409);
        }

        $recurso->delete();

        return response()->json(['mensaje' => 'Recurso eliminado']);
    }
}
