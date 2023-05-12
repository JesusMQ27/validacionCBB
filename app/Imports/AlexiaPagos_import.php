<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Providers\AlexiaPSecondSheetImport;

class AlexiaPagos_import implements ToModel, WithMultipleSheets {

    public function __construct($id, array $id_serie, $tabla_tmp) {
        $this->id = $id;
        $this->id_serie = $id_serie;
        $this->tabla_tmp = $tabla_tmp;
    }

    public function model(array $row) {
        /* return new Devengados([
          //
          ]); */
    }

    public function sheets(): array {

        return [
            'Datos' => new AlexiaPSecondSheetImport($this->id, $this->id_serie, $this->tabla_tmp),
        ];
    }

}
