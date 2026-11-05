<?php

use App\Http\Controllers\AuthController;
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
