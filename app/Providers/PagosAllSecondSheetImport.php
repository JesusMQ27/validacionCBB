<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Devengados;

class PagosAllSecondSheetImport implements ToCollection, WithStartRow {

    public function __construct($serie, $tabla) {
        $this->serie = $serie;
        $this->tabla = $tabla;
    }

    public function collection(Collection $rows) {
        $tabla = $this->tabla;
        foreach ($rows as $row) {
            if (trim($row[0]) == "") {
                break;
                return;
            }
            $doc_referencia_fecha = '';
            $fecha_cargo = $this->fecha_to_yyyy_mm_dd($row[0]);
            $fecha_venc = $this->fecha_to_yyyy_mm_dd($row[2]);
            $fecha_pago = $this->fecha_to_yyyy_mm_dd($row[4]);
            $fecha_emi = $this->fecha_to_yyyy_mm_dd($row[6]);
            $anio = explode("-", $fecha_cargo)[0];
            $matricula = $row[8];
            $serie = $row[9];
            $id_serie = Devengados::filtraSerie($serie, $this->serie);
            $numero = $row[10];
            //$pago_boleta = $serie . "-" . str_pad($numero, 8, "0", 0);
            $pago_boleta = $serie . "-" . $numero;
            $ruc_dni = $row[11];
            $cliente = strtr(strtoupper($row[12]), "áéíóúñ", "ÁÉÍÓÚÑ");
            $concepto = (string) trim($row[14]);
            $serie_ticke = $row[15];
            $descuento = $row[16];
            $base_imp = $row[17];
            $igv = $row[18];
            $total = $row[19];
            $cancelado = $row[21];
            $tc = $row[22];
            $tipo = $row[23];
            $centro = $row[25];
            $estado_compro = $row[27];
            $banco = $row[29];
            $doc_referencia = $row[35];
            if (trim($row[37]) !== '') {
                $doc_referencia_fecha = $this->fecha_to_yyyy_mm_dd($row[37]);
            } else {
                $doc_referencia_fecha = NULL;
            }

            Devengados::uploadAllPagos([
                "pago_anio" => $anio,
                "pago_fecha_emicar" => $fecha_cargo,
                "pago_fecha_venc" => $fecha_venc,
                "pago_fecha" => $fecha_pago,
                "pago_emision" => $fecha_emi,
                "pago_grado" => "$matricula",
                "id_serie" => $id_serie,
                "pago_num" => "$numero",
                "pago_boleta" => "$pago_boleta",
                "pago_dni" => "$ruc_dni",
                "pago_alumno" => "$cliente",
                "pago_concepto" => "$concepto",
                "pago_serie_ticke" => "$serie_ticke",
                "pago_dscto" => "$descuento",
                "pago_base_imp" => "$base_imp",
                "pago_igv" => "$igv",
                "pago_monto" => "$total",
                "pago_monto_cancelado" => "$cancelado",
                "pago_tc" => "$tc",
                "pago_tipo" => "$tipo",
                "pago_centro" => "$centro",
                "pago_estado_tipo" => "$estado_compro",
                "pago_banco" => "$banco",
                "pago_estado" => '1',
                "doc_referencia" => $doc_referencia,
                "doc_referencia_fecha" => $doc_referencia_fecha,
                    ], $tabla);
        }
        return 1;
    }

    public function startRow(): int {
        return 7;
    }

    function fecha_to_yyyy_mm_dd($fecha) {
        $fecha_inicial = explode("/", $fecha);
        $resultado = $fecha_inicial[2] . "-" . $fecha_inicial[1] . "-" . $fecha_inicial[0];
        return $resultado;
    }

    function multiexplode($delimiters, $string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

}
