<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Devengados;

class AlexiaPSecondSheetImport implements ToCollection, WithStartRow {

    public function __construct($id, $serie, $tabla_tmp) {
        $this->id = $id;
        $this->serie = $serie;
        $this->tabla_tmp = $tabla_tmp;
    }

    public function collection(Collection $rows) {
        $tabla_tmp = $this->tabla_tmp;
        foreach ($rows as $row) {
            if (trim($row[0]) == "") {
                break;
                return;
            }
            //if ($row[27] != "DEVENGADO") {
            if ($row[27] == "COBRADO") {//solo cobros
                $secondBean = new AlexiaSecondSheetImport(0, 0, 0);
                $fecha_cargo = $secondBean->fecha_to_yyyy_mm_dd($row[0]);
                $fecha_venc = $secondBean->fecha_to_yyyy_mm_dd($row[2]);
                $fecha_pago = $secondBean->fecha_to_yyyy_mm_dd($row[4]);
                $fecha_emi = $secondBean->fecha_to_yyyy_mm_dd($row[6]);
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
                $id_grupo = $this->id;

                //if (mb_strpos($concepto, "Pensión") || mb_strpos($concepto, "Pension")) {
                //echo  count(explode("Pensión", $concepto));
                //Jesus M;
                /* $concepto_mayus = mb_strtoupper($concepto, 'utf-8');
                  $cpension = count(explode("PENSION", $concepto_mayus));
                  if ($cpension < 2) {
                  $cpension = count(explode("PENSIÓN", $concepto_mayus));
                  } */
                $cpension = $secondBean->multiexplode(array("Pensión", "PENSION", "PENSIÓN"), $concepto);
                //Jesus M;
                if (count($cpension) > 0) {
                    Devengados::uploadPagosAlexia_tmp([
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
                        "id_grupo_pago" => $id_grupo
                            ], $tabla_tmp);
                }
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

    function multiexplode($delimiters, $string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

}
