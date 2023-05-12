<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Providers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Contabilidad;

class FirstSheetImportBanco implements ToCollection, WithStartRow {

    public function __construct($id) {
        $this->id = $id;
    }

    public function collection(Collection $rows) {
        foreach ($rows as $row) {
            if (trim($row[0]) == "") {
                break;
                return;
            }
            $scotiabank = new FirstSheetImportBanco($this->id);
            $servicio = $row[0];
            $usuario_dni = $row[1];
            $usuario_nombre = $row[2];
            $documento = $row[3];
            $fecha_vencimiento = $scotiabank->fecha_to_yyyy_mm_dd($row[4]);
            $moneda = $row[5];
            $importe = (double) $row[6];
            $mora = (double) $row[7];
            $fecha_proceso = $scotiabank->fecha_to_yyyy_mm_dd($row[8]);
            $hora_proceso = $row[9];
            $fecha_pago = $scotiabank->fecha_to_yyyy_mm_dd($row[10]);
            $forma_pago = $row[11];
            $oficina = $row[12];
            $operacion = $row[13];
            $referencia = $row[14];

            Contabilidad::carga_temporal_archivos("tmp_lista_archivo_" . $this->id, [
                'arc_alumno' => $usuario_nombre,
                'arc_dni' => trim($usuario_dni),
                'arc_servicio' => trim($servicio),
                'arc_documento' => trim($documento),
                'arc_vencimiento' => $fecha_vencimiento,
                'arc_moneda' => $moneda,
                'arc_importe_origen' => $importe,
                'arc_importe_depositado' => $importe,
                'arc_importe_mora' => $mora,
                'arc_fecha_proceso' => $fecha_proceso,
                'arc_hora_proceso' => $hora_proceso,
                'arc_fecha_pago' => $fecha_pago,
                'arc_forma_pago' => $forma_pago,
                'arc_oficina' => $oficina,
                'arc_nro_operacion' => $operacion,
                'arc_referencia' => $referencia,
                'arc_banco' => 'SCOTIABANK',
                'arc_estado' => '1'
            ]);
        }
        return 1;
    }

    public function startRow(): int {
        return 8;
    }

    function fecha_to_yyyy_mm_dd($fecha) {
        $fecha_inicial = explode("/", $fecha);
        $resultado = $fecha_inicial[2] . "-" . $fecha_inicial[1] . "-" . $fecha_inicial[0];
        return $resultado;
    }

}
