<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglaHonorario extends Model
{
    protected $table = 'reglas_honorarios';

    protected $fillable = ['umbral', 'porcentaje'];

    protected $casts = [
        'umbral' => 'float',
        'porcentaje' => 'float',
    ];
}
