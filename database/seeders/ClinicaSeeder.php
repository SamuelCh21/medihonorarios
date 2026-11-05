<?php

namespace Database\Seeders;

use App\Models\Atencion;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Recurso;
use App\Models\ReglaHonorario;
use App\Models\UsoRecurso;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClinicaSeeder extends Seeder
{
    public function run(): void
    {
        // especialidades
        $general = Especialidad::create(['nombre' => 'Medicina General']);
        $cardio = Especialidad::create(['nombre' => 'Cardiología']);
        $trauma = Especialidad::create(['nombre' => 'Traumatología']);
        $gineco = Especialidad::create(['nombre' => 'Ginecología']);
        $pediatria = Especialidad::create(['nombre' => 'Pediatría']);

        // reglas de honorarios (el porcentaje es lo que se lleva el médico)
        ReglaHonorario::create(['umbral' => 0, 'porcentaje' => 50]);
        ReglaHonorario::create(['umbral' => 30, 'porcentaje' => 60]);
        ReglaHonorario::create(['umbral' => 80, 'porcentaje' => 70]);
        ReglaHonorario::create(['umbral' => 200, 'porcentaje' => 80]);

        // recursos de la clínica
        $quirofano = Recurso::create(['nombre' => 'Quirófano', 'tarifa_hora' => 40]);
        Recurso::create(['nombre' => 'Sala de procedimientos', 'tarifa_hora' => 25]);
        $ecografia = Recurso::create(['nombre' => 'Sala de ecografía', 'tarifa_hora' => 30]);

        // médicos (las cédulas son válidas)
        $carlos = Medico::create([
            'nombre' => 'Carlos Mena',
            'cedula' => '1710340652',
            'especialidad_id' => $trauma->id,
            'tipo_vinculacion' => 'externo',
            'user_id' => User::where('email', 'medico@test.com')->value('id'),
        ]);
        $lucia = Medico::create([
            'nombre' => 'Lucía Andrade',
            'cedula' => '1723456719',
            'especialidad_id' => $cardio->id,
            'tipo_vinculacion' => 'externo',
        ]);
        $andres = Medico::create([
            'nombre' => 'Andrés Salazar',
            'cedula' => '0926687856',
            'especialidad_id' => $general->id,
            'tipo_vinculacion' => 'externo',
        ]);
        Medico::create([
            'nombre' => 'Paola Vélez',
            'cedula' => '1801234566',
            'especialidad_id' => $pediatria->id,
            'tipo_vinculacion' => 'planta',
        ]);
        Medico::create([
            'nombre' => 'Marcela Rivas',
            'cedula' => '0134567817',
            'especialidad_id' => $gineco->id,
            'tipo_vinculacion' => 'planta',
        ]);

        // atenciones de la quincena del 01/10/2026 al 15/10/2026
        // Carlos Mena: bruto 302.50, descuento 80.00, neto a transferir 222.50
        $atenciones = [
            [$carlos, '2026-10-02', 'Consulta general', 25],
            [$carlos, '2026-10-05', 'Consulta especialista', 45],
            [$carlos, '2026-10-08', 'Ecografía', 90],
            [$carlos, '2026-10-12', 'Cirugía menor', 250],
            [$lucia, '2026-10-03', 'Consulta especialista', 60],
            [$lucia, '2026-10-10', 'Ecografía', 85],
            [$andres, '2026-10-04', 'Consulta general', 20],
        ];

        foreach ($atenciones as [$medico, $fecha, $servicio, $monto]) {
            Atencion::create([
                'medico_id' => $medico->id,
                'fecha_atencion' => $fecha,
                'servicio' => $servicio,
                'monto_cobrado' => $monto,
            ]);
        }

        // uso de recursos
        UsoRecurso::create(['medico_id' => $carlos->id, 'recurso_id' => $quirofano->id, 'fecha_uso' => '2026-10-12', 'horas' => 2]);
        UsoRecurso::create(['medico_id' => $lucia->id, 'recurso_id' => $ecografia->id, 'fecha_uso' => '2026-10-10', 'horas' => 1]);
    }
}
