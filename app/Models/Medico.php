<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $fillable = ['nombre', 'cedula', 'especialidad_id', 'tipo_vinculacion', 'user_id'];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function atenciones()
    {
        return $this->hasMany(Atencion::class);
    }

    public function usosRecurso()
    {
        return $this->hasMany(UsoRecurso::class);
    }
}
