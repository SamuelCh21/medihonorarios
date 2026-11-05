<?php

namespace App\Http\Controllers;

use App\Models\UsoRecurso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsoRecursoController extends Controller
{
    // READ
    public function index()
    {
        return UsoRecurso::with('medico', 'recurso')->orderByDesc('fecha_uso')->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        return UsoRecurso::create($datos)->load('medico', 'recurso');
    }

    // UPDATE
    public function update(Request $request, UsoRecurso $uso)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        $uso->update($datos);

        return $uso->load('medico', 'recurso');
    }

    // DELETE
    public function destroy(UsoRecurso $uso)
    {
        $uso->delete();

        return response()->json(['mensaje' => 'Uso de recurso eliminado']);
    }

    private function reglas(): array
    {
        return [
            // a los médicos de planta no se les cobra el uso de recursos
            'medico_id' => ['required', Rule::exists('medicos', 'id')->where('tipo_vinculacion', 'externo')],
            'recurso_id' => 'required|exists:recursos,id',
            'fecha_uso' => 'required|date',
            'horas' => 'required|numeric|min:0.01|max:24',
        ];
    }

    private function mensajes(): array
    {
        return [
            'medico_id.required' => 'Escoge un médico.',
            'medico_id.exists' => 'El médico no existe o no es externo.',
            'recurso_id.required' => 'Escoge un recurso.',
        ];
    }
}
