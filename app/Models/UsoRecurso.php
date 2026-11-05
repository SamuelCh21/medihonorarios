<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsoRecurso extends Model
{
    protected $table = 'usos_recurso';

    protected $fillable = ['medico_id', 'recurso_id', 'fecha_uso', 'horas'];

    protected $casts = [
        'fecha_uso' => 'date:Y-m-d',
        'horas' => 'float',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }
}
