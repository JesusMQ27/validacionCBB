<?php

namespace App\Imports;

use App\Contabilidad;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

/**
 * Description of FacturacionImport
 *
 * @author usuarioA
 */
class FacturacionImport implements ToModel, WithStartRow, WithValidation {

    public function __construct($id) {
        $this->id = $id;
    }

    public function model(array $row) {
        $comprobante = $row[0];
        $fecha = $row[1];
        $serie = $row[2];
        $numero = $row[3];
        $tipoDocumento = $row[4];
        $documentoDetalle = $row[5];
        $numeroAfecto = $row[6];
        $fechaNumeroAfectado = $row[7];
        $documento = $row[8];
        $nombres = $row[9];
        $direccion = mb_strtoupper($row[10]);
        $opeGravada = (double) $row[11];
        $opeInafecta = (double) $row[12];
        $opeExonerada = (double) $row[13];
        $opeGratuitas = (double) $row[14];
        $totalVenta = (double) $row[15];
        $descripcion = $row[16];
        $cantidad = $row[17];
        $valor = (double) $row[18];
        $venta = (double) $row[19];

        return Contabilidad::inserta_tmp_facturacion("tmp_cbb_facturacion_" . $this->id, [
                    'fact_boleta' => $comprobante,
                    'fact_fecha' => $fecha,
                    'fact_serie' => $serie,
                    'fact_numero' => $numero,
                    'fact_tipo_documento' => $tipoDocumento,
                    'fact_detalle' => $documentoDetalle,
                    'fact_num_afectado' => $numeroAfecto,
                    'fact_fech_afectado' => $fechaNumeroAfectado,
                    'fact_dni' => $documento,
                    'fact_nombres' => trim($nombres),
                    'fact_direccion' => trim($direccion),
                    'fact_ope_gravadas' => trim($opeGravada),
                    'fact_ope_inafectas' => $opeInafecta,
                    'fact_ope_exoneradas' => $opeExonerada,
                    'fact_ope_gratuitas' => $opeGratuitas,
                    'fact_venta' => $totalVenta,
                    'fact_descripcion' => trim($descripcion),
                    'fact_cantidad' => $cantidad,
                    'fact_valor' => $valor,
                    'fact_ventas' => $venta
        ]);
    }

    public function rules(): array {
        return [
            'date' => Rule::in(['date', 'required']),
            // Above is alias for as it always validates in batches
            '*.email' => Rule::in(['patrick@maatwebsite.nl']),
        ];
    }

    public function startRow(): int {
        return 4;
    }

}
