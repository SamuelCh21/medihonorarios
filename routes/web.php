<?php

use Illuminate\Support\Facades\Route;

// página que carga Vue
Route::get('/', function () {
    return view('app');
});
