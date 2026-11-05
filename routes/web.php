<?php

use App\Http\Controllers\AtencionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\ReglaHonorarioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsoRecursoController;
use Illuminate\Support\Facades\Route;

// páginas que cargan Vue
Route::get('/', function () {
    return view('app');
});

Route::get('/login', function () {
    return view('app');
})->name('login');

// rutas del login (públicas)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/me', [AuthController::class, 'me']);

// administración: solo entra quien tenga sesión y rol admin
Route::middleware(['auth', 'role:admin'])->prefix('api')->group(function () {
    // apiResource crea index, store, update y destroy de cada tabla
    Route::apiResource('usuarios', UserController::class)->except('show')->parameters(['usuarios' => 'user']);
    Route::apiResource('especialidades', EspecialidadController::class)->except('show')->parameters(['especialidades' => 'especialidad']);
    Route::apiResource('medicos', MedicoController::class)->except('show')->parameters(['medicos' => 'medico']);
    Route::apiResource('reglas-honorarios', ReglaHonorarioController::class)->except('show')->parameters(['reglas-honorarios' => 'regla']);
    Route::apiResource('recursos', RecursoController::class)->except('show')->parameters(['recursos' => 'recurso']);
    Route::apiResource('atenciones', AtencionController::class)->except('show')->parameters(['atenciones' => 'atencion']);
    Route::apiResource('usos-recurso', UsoRecursoController::class)->except('show')->parameters(['usos-recurso' => 'uso']);
});
