<?php

namespace App\Imports;

use App\Contabilidad;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class EstadoCuentaImport implements ToModel, WithStartRow, WithValidation {

    public function __construct($id) {
        $this->id = $id;
    }

    public function model(array $row) {
        $estado_cuenta = new EstadoCuentaImport($this->id);
        //print_r($row);
        $servicio = $row[0];
        $usuario_dni = $row[1];
        $usuario_nombre = $row[2];
        $documento = $row[3];
        $fecha_vencimiento = $estado_cuenta->fecha_to_yyyy_mm_dd($row[4]);
        $moneda = $row[5];
        $importe = (double) $row[6];
        $mora = (double) $row[7];
        $fecha_proceso = $estado_cuenta->fecha_to_yyyy_mm_dd($row[8]);
        $hora_proceso = $row[9];
        $fecha_pago = $estado_cuenta->fecha_to_yyyy_mm_dd($row[10]);
        $forma_pago = $row[11];
        $oficina = $row[12];
        $operacion = $row[13];
        $referencia = $row[14];
        $tipo_banco = $row[15];

        return Contabilidad::carga_estado_cuenta("tmp_estado_cuenta_" . $this->id, [
                    'ec_servicio' => $servicio,
                    'ec_dni' => trim($usuario_dni),
                    'ec_nombre' => $usuario_nombre,
                    'ec_documento' => trim($documento),
                    'ec_fecha_venci' => $fecha_vencimiento,
                    'ec_moneda' => $moneda,
                    'ec_total' => "",
                    'ec_importe' => $importe,
                    'ec_mora' => $mora,
                    'ec_fecha_proce' => $fecha_proceso,
                    'ec_fecha_pago' => $fecha_pago,
                    'ec_forma_pago' => $forma_pago,
                    'ec_oficina' => $oficina,
                    'ec_operacion' => $operacion,
                    'ec_referencia' => $referencia,
                    'ec_tipo_banco' => trim($tipo_banco),
                    'ec_estado' => '1'
        ]);

        /* return new Contabilidad([
          //
          ]); */
    }

    public function startRow(): int {
        return 4;
    }

    public function rules(): array {
        return [
            'date' => Rule::in(['date', 'required']),
            // Above is alias for as it always validates in batches
            '*.email' => Rule::in(['patrick@maatwebsite.nl']),
        ];
    }

    function fecha_to_yyyy_mm_dd($fecha) {
        $fecha_inicial = explode("/", $fecha);
        $resultado = $fecha_inicial[2] . "-" . $fecha_inicial[1] . "-" . $fecha_inicial[0];
        return $resultado;
    }

}
