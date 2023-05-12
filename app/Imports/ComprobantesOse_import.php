<?php
/*chinitos*/
namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Providers\ComprobantesOseSheetImport;

class ComprobantesOse_import implements ToModel, WithMultipleSheets {

    public function __construct($tabla_tmp) {
        $this->tabla_tmp = $tabla_tmp;
    }

    public function model(array $row) {
        /* return new Devengados([
          //
          ]); */
    }

    public function sheets(): array {
        return [
            'DETALLE' => new ComprobantesOseSheetImport($this->tabla_tmp)
        ];
    }

}
