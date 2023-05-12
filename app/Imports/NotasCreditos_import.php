<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Providers\NotasCreditosSecondSheetImport;

class NotasCreditos_import implements ToModel, WithMultipleSheets {

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
            'Datos' => new NotasCreditosSecondSheetImport($this->id, $this->id_serie, $this->tabla_tmp),
        ];
    }

}
