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

class SecondSheetImportConcarNotasCredito implements ToCollection, WithStartRow {

    public function __construct($id) {
        $this->id = $id;
    }

    public function collection(Collection $rows) {
        foreach ($rows as $row) {
            if (trim($row[0]) == "") {
                break;
                return;
            }
            $secondBean = new SecondSheetImportConcarNotasCredito(0);
            $fecha_cargo = $row[0];
            $fecha_venc = $row[2];
            $fecha_pago = $row[4];
            $fecha_emi = $row[6];
            $matricula = $row[8];
            $serie = $row[9];
            $numero = $row[10];
            $ruc_dni = $row[11];
            $cliente = strtoupper($row[12]);
            $concepto = $row[14];
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
            $observaciones = $row[31];
            $avisoCobro = $row[33];
            $documentoReferencia = $row[35];
            $fechaEmision = $row[37];
//echo Date::excelToDateTimeObject($fecha_cargo)->format("Y-m-d");
            if (trim($tipo) === "NC BOL" || trim($tipo) === "NC FAC") {
                Contabilidad::carga_pagos_alexia_concar_notas_credito("tmp_pagos_alexia_concar_notascredito_" . $this->id, [
                    'pa_fecha_cargo' => $secondBean->fecha_to_yyyy_mm_dd($fecha_cargo),
                    'pa_fecha_venc' => $secondBean->fecha_to_yyyy_mm_dd($fecha_venc),
                    'pa_fecha_pago' => $secondBean->fecha_to_yyyy_mm_dd($fecha_pago),
                    'pa_fecha_emi' => $secondBean->fecha_to_yyyy_mm_dd($fecha_emi),
                    'pa_matricula' => $matricula,
                    'pa_serie' => $serie,
                    'pa_numero' => $numero,
                    'pa_ruc_dni' => trim($ruc_dni),
                    'pa_cliente' => $cliente,
                    'pa_concepto' => $secondBean::eliminar_acentos($concepto),
                    'pa_serie_ticke' => $serie_ticke,
                    'pa_descuento' => $descuento,
                    'pa_base_imp' => $base_imp,
                    'pa_igv' => $igv,
                    'pa_total' => $total,
                    'pa_cancelado' => $cancelado,
                    'pa_tc' => $tc,
                    'pa_tipo' => $tipo,
                    'pa_centro' => $centro,
                    'pa_estado_compro' => $estado_compro,
                    'pa_banco' => $banco,
                    'pa_observaciones' => $observaciones,
                    'pa_navisos_cobro' => $avisoCobro,
                    'pa_doc_referencia' => $documentoReferencia,
                    'pa_fecha_emision_referencia' => $secondBean->fecha_to_yyyy_mm_dd($fechaEmision),
                    'pa_estado' => '1'
                ]);
            }
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

    function eliminar_acentos($cadena) {
        //Reemplazamos la A y a
        $cadena = str_replace(
                array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'), array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'), $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
                array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'), array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'), $cadena);

        //Reemplazamos la I y i
        $cadena = str_replace(
                array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'), array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'), $cadena);

        //Reemplazamos la O y o
        $cadena = str_replace(
                array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'), array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'), $cadena);

        //Reemplazamos la U y u
        $cadena = str_replace(
                array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'), array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'), $cadena);

        return $cadena;
    }

}
