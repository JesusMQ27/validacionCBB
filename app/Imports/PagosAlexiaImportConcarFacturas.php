<?php

//Chinita

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Providers\SecondSheetImportConcarFacturas;

class PagosAlexiaImportConcarFacturas implements ToModel, WithStartRow, WithValidation, WithMultipleSheets {

    public function __construct($id) {
        $this->id = $id;
    }

    public function model(array $row) {
        //print_r($row);
    }

    public function rules(): array {
        return [
            'date' => Rule::in(['date', 'required']),
            // Above is alias for as it always validates in batches
            '*.email' => Rule::in(['patrick@maatwebsite.nl']),
        ];
    }

    public function startRow(): int {
        return 7;
    }

    public function sheets(): array {
        return [
            'Datos' => new SecondSheetImportConcarFacturas($this->id),
        ];
    }

}
