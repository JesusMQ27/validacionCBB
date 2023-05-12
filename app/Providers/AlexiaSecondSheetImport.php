<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Devengados;

class AlexiaSecondSheetImport implements ToCollection, WithStartRow {

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
            //$deve_boleta = $serie . "-" . str_pad($numero, 8, "0", 0);
            $deve_boleta = $serie . "-" . $numero;
            $ruc_dni = $row[11];
            $cliente = strtr(strtoupper($row[12]), "áéíóúñ", "ÁÉÍÓÚÑ");
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
            $id_grupo = $this->id;
            //if (strpos($concepto, "Pensión") == false) {
            //$cpension = count(explode("Pensión", $concepto));
            $cpension = $secondBean->multiexplode(array("Pensión", "PENSION", "PENSIÓN"), $concepto);
            if (count($cpension) > 0) {
                //Devengados::uploadDevengadosAlexia([
                Devengados::uploadDevengadosAlexia_tmp([
                    'deve_anio' => "$anio",
                    'deve_fecha_emicar' => $fecha_cargo,
                    'deve_fecha_venc' => $fecha_venc,
                    'deve_fecha_pag' => $fecha_pago,
                    'deve_fecha' => $fecha_emi,
                    'deve_grado' => "$matricula",
                    'id_serie' => $id_serie,
                    'deve_num' => "$numero",
                    'deve_boleta' => "$deve_boleta",
                    'deve_dni' => "$ruc_dni",
                    'deve_alumno' => "$cliente",
                    'deve_concepto' => "$concepto",
                    'deve_serie_ticke' => "$serie_ticke",
                    'deve_dscto' => "$descuento",
                    'deve_base_imp' => "$base_imp",
                    'deve_igv' => "$igv",
                    'deve_monto' => "$total",
                    'deve_monto_cancelado' => "$cancelado",
                    'deve_tc' => "$tc",
                    'deve_tipo' => "$tipo",
                    'deve_centro' => "$centro",
                    'deve_estado_tipo' => "$estado_compro",
                    'deve_banco' => "$banco",
                    'deve_estado' => '1',
                    'id_grupo' => $id_grupo
                        ], $tabla_tmp);
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
