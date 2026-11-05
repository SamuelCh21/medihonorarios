<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // iniciar sesión
    public function login(Request $request)
    {
        $credenciales = $request->only('email', 'password');

        if (! Auth::attempt($credenciales)) {
            return response()->json(['error' => 'Datos incorrectos'], 401);
        }

        $request->session()->regenerate(); // aquí "recuerda" al usuario

        return response()->json(['mensaje' => 'Login correcto']);
    }

    // cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['mensaje' => 'Sesión cerrada']);
    }

    // para que Vue sepa si hay sesión
    public function me()
    {
        return response()->json([
            'logueado' => Auth::check(),
            'nombre' => Auth::user()?->name,
            'role' => Auth::user()?->role,
        ]);
    }
}
