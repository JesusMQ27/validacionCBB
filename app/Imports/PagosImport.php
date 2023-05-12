<?php

namespace App\Imports;

use App\Devengados;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class PagosImport implements ToModel, WithStartRow, WithValidation {

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
        $pago_fecha = Date::excelToDateTimeObject($row[8])->format('Y-m-d');
        $pago_anio = explode("-", $fecha['fecha'])[0];
        $pago_serie = explode("-", $row[4]);
        $pago_num = (int) $pago_serie[1];
        $pago_boleta = $pago_serie[0] . "-" . str_pad($pago_serie[1], 8, "0", 0);
        $id_serie = Devengados::filtraSerie($pago_serie[0], $this->id_serie);
        //Devengados::update_pago_devengado($row[4]);
        Devengados::update_pago_devengado($pago_boleta);
        return Devengados::uploadPagos([
                    'pago_grado' => $row[1],
                    'pago_anio' => $pago_anio,
                    'pago_emision' => $fecha['fecha'],
                    'pago_dni' => $row[3],
                    //'id_serie' => $pago_serie[0],
                    'id_serie' => $id_serie,
                    'pago_num' => $pago_num,
                    'pago_boleta' => $pago_boleta,
                    'pago_alumno' => $row[5],
                    'pago_cuota' => $row[6],
                    'pago_monto' => $row[7],
                    'pago_fecha' => $pago_fecha,
                    'id_grupo_pago' => $this->id,
                    'pago_estado' => '1',
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
