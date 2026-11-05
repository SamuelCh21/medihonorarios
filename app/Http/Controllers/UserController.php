<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // READ (la contraseña nunca se devuelve porque el modelo la tiene oculta)
    public function index()
    {
        return User::orderBy('name')->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'role' => 'required|in:admin,gerencia,medico',
        ]);

        $datos['password'] = Hash::make($datos['password']);

        return User::create($datos);
    }

    // UPDATE: si la contraseña viene vacía, se deja la que tenía
    public function update(Request $request, User $user)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:4',
            'role' => 'required|in:admin,gerencia,medico',
        ]);

        if (empty($datos['password'])) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }

        $user->update($datos);

        return $user;
    }

    // DELETE: un admin no puede borrarse a sí mismo
    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario'], 409);
        }

        $user->delete();

        return response()->json(['mensaje' => 'Usuario eliminado']);
    }
}
