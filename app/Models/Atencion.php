<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    protected $table = 'atenciones';

    protected $fillable = ['medico_id', 'fecha_atencion', 'servicio', 'monto_cobrado'];

    protected $casts = [
        'fecha_atencion' => 'date:Y-m-d',
        'monto_cobrado' => 'float',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
