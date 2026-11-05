<?php

// mensajes de validación en español (solo los que usamos en el proyecto)
return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'unique' => 'Ese :attribute ya está registrado.',
    'exists' => 'El :attribute escogido no es válido.',
    'in' => 'El :attribute escogido no es válido.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor a :max.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo',
        'password' => 'contraseña',
        'role' => 'rol',
        'cedula' => 'cédula',
        'especialidad_id' => 'especialidad',
        'tipo_vinculacion' => 'tipo de vinculación',
        'user_id' => 'usuario',
        'medico_id' => 'médico',
        'recurso_id' => 'recurso',
        'fecha_atencion' => 'fecha de atención',
        'fecha_uso' => 'fecha de uso',
        'monto_cobrado' => 'monto cobrado',
        'tarifa_hora' => 'tarifa por hora',
        'umbral' => 'umbral',
        'porcentaje' => 'porcentaje',
    ],
];
