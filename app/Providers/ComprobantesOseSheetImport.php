<?php

//chinitos

namespace App\Providers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Devengados;

class ComprobantesOseSheetImport implements ToCollection, WithStartRow {

    public function __construct($tabla_tmp) {
        $this->tabla_tmp = $tabla_tmp;
    }

    public function collection(Collection $rows) {
        $tabla_tmp = $this->tabla_tmp;

        foreach ($rows as $row) {
            if (trim($row[0]) == "") {
                break;
                return;
            }
            $comproOse = new ComprobantesOseSheetImport(0);
            $nroComprobante = $row[1];
            $doc_iden = $row[2];
            $serie = $row[3];
            $numero = $row[4];
            $fecha_envio = $row[5];
            $descuento = $row[6];
            $codInterno = $row[7];
            $nombres = strtoupper($row[8]);
            $recargo = $row[10];
            $gratuito = $row[11];
            $igv = $row[12];
            $isc = $row[13];
            $neto = $row[14];
            $total = $row[15];
            $tipo_moneda = $row[16];
            $tipo_cambio = $row[17];
            $total_otra = $row[18];
            $observacion = $row[19];
            $pagado = $row[20];
            $sucursal = $row[21];
            $adicional1 = $row[22];
            $adicional2 = $row[23];
            $adicional3 = $row[24];
            $adicional4 = $row[25];
            $adicional5 = $row[26];
            $adicional6 = $row[27];
            $usuario = $row[28];
            $fecha_creacion = $comproOse->fecha_to_yyyy_mm_dd($row[29]);
            $c_baja = $row[20];

            Devengados::uploadComprobantesOse_tmp([
                "com_tipo_documento" => $nroComprobante,
                "com_doc_iden" => $doc_iden,
                "com_serie" => $serie,
                "com_numero" => $numero,
                "com_fecha_envio" => $fecha_envio,
                "com_descuento" => $descuento,
                "com_cod_interno" => $codInterno,
                "com_nombres" => $nombres,
                "com_recargo" => $recargo,
                "com_gratuito" => $gratuito,
                "com_igv" => $igv,
                "com_isc" => $isc,
                "com_neto" => $neto,
                "com_total" => $total,
                "com_tip_moneda" => $tipo_moneda,
                "com_tip_cambio" => $tipo_cambio,
                "com_tot_otra_moneda" => $total_otra,
                "com_observacion" => $observacion,
                "com_pagado" => $pagado,
                "com_sucursal" => $sucursal,
                "com_adicional_1" => $adicional1,
                "com_adicional_2" => $adicional2,
                "com_adicional_3" => $adicional3,
                "com_adicional_4" => $adicional4,
                "com_adicional_5" => $adicional5,
                "com_adicional_6" => $adicional6,
                "com_usuario" => $usuario,
                "com_fecha_creado" => $fecha_creacion,
                "com_baja" => $c_baja,
                "id_grupo" => 0,
                "com_estado" => '1'
                    ], $tabla_tmp);
        }
        return 1;
    }

    public function startRow(): int {
        return 2;
    }

    function fecha_to_yyyy_mm_dd($fecha) {
        $fecha_inicial = explode("-", $fecha);
        $resultado = $fecha_inicial[2] . "-" . $fecha_inicial[1] . "-" . $fecha_inicial[0];
        return $resultado;
    }

}
