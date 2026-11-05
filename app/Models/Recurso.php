<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    protected $fillable = ['nombre', 'tarifa_hora'];

    protected $casts = [
        'tarifa_hora' => 'float',
    ];

    public function usos()
    {
        return $this->hasMany(UsoRecurso::class);
    }
}
