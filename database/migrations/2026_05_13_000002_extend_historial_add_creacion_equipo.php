<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE historial_movimientos
            MODIFY COLUMN tipo_accion ENUM(
                'solicitud_aula',
                'aprobacion_aula',
                'cancelacion_aula',
                'confirmacion_asistencia',
                'liberacion_por_tolerancia',
                'finalizacion_aula',
                'prestamo_equipo',
                'prestamo_equipo_creado',
                'entrega_equipo',
                'devolucion_equipo',
                'asignacion_equipo_aula',
                'asignacion_ti',
                'devolucion_ti',
                'registro_novedad',
                'cambio_estado_equipo',
                'cambio_estado_aula',
                'accion_sistema',
                'acceso_login',
                'acceso_logout',
                'creacion_equipo'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE historial_movimientos
            MODIFY COLUMN tipo_accion ENUM(
                'solicitud_aula',
                'aprobacion_aula',
                'cancelacion_aula',
                'confirmacion_asistencia',
                'liberacion_por_tolerancia',
                'finalizacion_aula',
                'prestamo_equipo',
                'prestamo_equipo_creado',
                'entrega_equipo',
                'devolucion_equipo',
                'asignacion_equipo_aula',
                'asignacion_ti',
                'devolucion_ti',
                'registro_novedad',
                'cambio_estado_equipo',
                'cambio_estado_aula',
                'accion_sistema',
                'acceso_login',
                'acceso_logout'
            ) NOT NULL
        ");
    }
};
