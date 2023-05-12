<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Devengados;

class NotasCreditosSecondSheetImport implements ToCollection, WithStartRow {

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
            if ($row[27] == "COMPENSADO" && (trim($row[23]) == 'NC BOL' || trim($row[23]) == 'NC FAC')) {//solo compensados
                $secondBean = new NotasCreditosSecondSheetImport(0, 0, 0);
                $fecha_cargo = $secondBean->fecha_to_yyyy_mm_dd($row[0]);
                $fecha_venc = $secondBean->fecha_to_yyyy_mm_dd($row[2]);
                $fecha_pago = $secondBean->fecha_to_yyyy_mm_dd($row[4]);
                $fecha_emi = $secondBean->fecha_to_yyyy_mm_dd($row[6]);
                $anio = explode("-", $fecha_cargo)[0];
                $matricula = $row[8];
                $serie = $row[9];
                $id_serie = Devengados::filtraSerie($serie, $this->serie);
                $numero = $row[10];
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
                $observaciones = $row[31];
                if (isset($row[33])) {
                    $nro_avisos = $row[33];
                } else {
                    $nro_avisos = '';
                }

                $id_grupo = $this->id;
                $cpension = $secondBean->multiexplode(array("Nota de Crédito", "NOTA DE CREDITO", "NOTA DE CRÉDITO"), $concepto);
                if (count($cpension) > 0) {
                    Devengados::uploadNotasCreditos_tmp([
                        "nota_anio" => $anio,
                        "nota_fecha_emicar" => $fecha_cargo,
                        "nota_fecha_venc" => $fecha_venc,
                        "nota_fecha" => $fecha_pago,
                        "nota_emision" => $fecha_emi,
                        "nota_grado" => "$matricula",
                        "id_serie" => $id_serie,
                        "nota_num" => "$numero",
                        "nota_boleta" => "$pago_boleta",
                        "nota_dni" => "$ruc_dni",
                        "nota_alumno" => "$cliente",
                        "nota_concepto" => "$concepto",
                        "nota_serie_ticke" => "$serie_ticke",
                        "nota_dscto" => "$descuento",
                        "nota_base_imp" => "$base_imp",
                        "nota_igv" => "$igv",
                        "nota_total" => "$total",
                        "nota_monto_cancelado" => "$cancelado",
                        "nota_tc" => "$tc",
                        "nota_tipo" => "$tipo",
                        "nota_centro" => "$centro",
                        "nota_estado_tipo" => "$estado_compro",
                        "nota_banco" => "$banco",
                        "nota_observaciones" => "$observaciones",
                        "nota_nro_avisos" => "$nro_avisos",
                        "nota_estado" => '1',
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
