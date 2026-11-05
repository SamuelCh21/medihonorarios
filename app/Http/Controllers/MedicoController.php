<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Rules\CedulaEcuatoriana;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicoController extends Controller
{
    // READ: listar médicos (se pueden filtrar por especialidad y por tipo de vinculación)
    public function index(Request $request)
    {
        $consulta = Medico::with('especialidad', 'user')->orderBy('nombre');

        if ($request->filled('especialidad_id')) {
            $consulta->where('especialidad_id', $request->especialidad_id);
        }

        if ($request->filled('tipo_vinculacion')) {
            $consulta->where('tipo_vinculacion', $request->tipo_vinculacion);
        }

        return $consulta->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        return Medico::create($datos)->load('especialidad', 'user');
    }

    // UPDATE
    public function update(Request $request, Medico $medico)
    {
        $datos = $request->validate($this->reglas($medico), $this->mensajes());

        $medico->update($datos);

        return $medico->load('especialidad', 'user');
    }

    // DELETE: no se borra si ya tiene atenciones o usos de recurso
    public function destroy(Medico $medico)
    {
        if ($medico->atenciones()->exists() || $medico->usosRecurso()->exists()) {
            return response()->json(['message' => 'No se puede eliminar: el médico ya tiene atenciones o usos de recurso'], 409);
        }

        $medico->delete();

        return response()->json(['mensaje' => 'Médico eliminado']);
    }

    // la cédula es el dato sensible: se valida aquí en el servidor antes de guardar
    private function reglas(?Medico $medico = null): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'cedula' => [
                'required',
                new CedulaEcuatoriana,
                Rule::unique('medicos', 'cedula')->ignore($medico?->id),
            ],
            'especialidad_id' => 'required|exists:especialidades,id',
            'tipo_vinculacion' => 'required|in:externo,planta',
            // el usuario es opcional y tiene que ser de rol médico y no estar usado por otro médico
            'user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', 'medico'),
                Rule::unique('medicos', 'user_id')->ignore($medico?->id),
            ],
        ];
    }

    private function mensajes(): array
    {
        return [
            'cedula.unique' => 'Ya existe un médico con esa cédula.',
            'especialidad_id.required' => 'Escoge una especialidad.',
            'especialidad_id.exists' => 'La especialidad no existe.',
            'user_id.exists' => 'El usuario no existe o no tiene rol de médico.',
            'user_id.unique' => 'Ese usuario ya está enlazado a otro médico.',
        ];
    }
}
