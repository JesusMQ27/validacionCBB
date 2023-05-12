<?php

namespace App\Imports;

use App\Devengados;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class DevengadosImport implements ToModel, WithStartRow, WithValidation {

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function __construct($id, array $id_serie) {
        $this->id = $id;
        $this->id_serie = $id_serie;
    }

    public function model(array $row) {
        $fecha['fecha'] = Date::excelToDateTimeObject($row[2])->format('Y-m-d');
        $deve_anio = explode("-", $fecha['fecha'])[0];
        $deve_serie = explode("-", $row[4]);
        if (count($deve_serie) != 1) {
            $deve_num = (int) $deve_serie[1];
            //$deve_boleta = $deve_serie[0] . "-" . str_pad($deve_serie[1], 8, "0", 0);
            $deve_boleta = $deve_serie[0] . "-" . $deve_serie[1];
        } else {
            $deve_num = "";
            $deve_boleta = "";
        }
        $id_serie = Devengados::filtraSerie($deve_serie[0], $this->id_serie);
        //
        is_null($row[1]) ? $deve_grado = "" : $deve_grado = $row[1];
        is_null($row[3]) ? $deve_dni = "" : $deve_dni = $row[3];
        $id_grupo = $this->id;
        is_null($row[5]) ? $deve_alumno = "" : $deve_alumno = strtoupper($row[5]);
        is_null($row[6]) ? $deve_cuota = "" : $deve_cuota = $row[6];
        is_null($row[7]) ? $deve_monto = "" : $deve_monto = $row[7];

        return Devengados::uploadDevengados([
                    'deve_anio' => $deve_anio,
                    'deve_grado' => "$deve_grado",
                    'deve_fecha' => $fecha['fecha'],
                    'deve_dni' => "$deve_dni",
                    //'id_serie' => $deve_serie[0],
                    'id_serie' => $id_serie,
                    'deve_num' => "$deve_num",
                    'deve_boleta' => "$deve_boleta",
                    'id_grupo' => $id_grupo,
                    'deve_alumno' => "$deve_alumno",
                    'deve_cuota' => "$deve_cuota",
                    'deve_monto' => $deve_monto,
                    'deve_estado' => '1'
        ]);
    }

    public function startRow(): int {
        return 2;
    }

    public function rules(): array {

        return [
            'date' => Rule::in(['date', 'required']),
            // Above is alias for as it always validates in batches
            '*.email' => Rule::in(['patrick@maatwebsite.nl']),
        ];
    }

}
