<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Providers\PagosAllSecondSheetImport;

class PagosAllImport implements ToModel, WithMultipleSheets {

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function __construct(array $id_serie, $tabla) {
        $this->id_serie = $id_serie;
        $this->tabla = $tabla;
    }

    public function model(array $row) {
    }

    public function sheets(): array {

        return [
            'Datos' => new PagosAllSecondSheetImport($this->id_serie, $this->tabla),
        ];
    }

}
