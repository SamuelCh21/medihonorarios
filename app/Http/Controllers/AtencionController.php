<?php

namespace App\Http\Controllers;

use App\Models\Atencion;
use Illuminate\Http\Request;

class AtencionController extends Controller
{
    // READ
    public function index()
    {
        return Atencion::with('medico.especialidad')->orderByDesc('fecha_atencion')->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        return Atencion::create($datos)->load('medico.especialidad');
    }

    // UPDATE
    public function update(Request $request, Atencion $atencion)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        $atencion->update($datos);

        return $atencion->load('medico.especialidad');
    }

    // DELETE
    public function destroy(Atencion $atencion)
    {
        $atencion->delete();

        return response()->json(['mensaje' => 'Atención eliminada']);
    }

    private function reglas(): array
    {
        return [
            'medico_id' => 'required|exists:medicos,id',
            'fecha_atencion' => 'required|date',
            'servicio' => 'required|string|max:255',
            'monto_cobrado' => 'required|numeric|min:0.01',
        ];
    }

    private function mensajes(): array
    {
        return [
            'medico_id.required' => 'Escoge un médico.',
            'medico_id.exists' => 'El médico no existe.',
        ];
    }
}
