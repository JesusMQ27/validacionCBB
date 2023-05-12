<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Providers\AlexiaSecondSheetImport;

class AlexiaDevengados_import implements ToModel, WithMultipleSheets {

    public function __construct($id, array $id_serie, $tabla_tmp) {
        $this->id = $id;
        $this->id_serie = $id_serie;
        $this->tabla_tmp = $tabla_tmp;
    }

    public function model(array $row) {
        //dd($row);
        /* return new Devengados([
          //
          ]); */
    }

    public function sheets(): array {
        return [
            'Datos' => new AlexiaSecondSheetImport($this->id, $this->id_serie, $this->tabla_tmp)
        ];
    }

}
