<?php

namespace App\Exports;

use App\Contabilidad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use \Maatwebsite\Excel\Concerns\Exportable;
//font and size alv
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
//
use Illuminate\Support\Facades\Request;

class ContabilidadExport implements FromCollection, WithHeadings, WithEvents {

    use Exportable;

    public function collection() {

        $fechaIni = Request::input("fechaInicio");
        $fechaFin = Request::input("fechaFin");
        $serie = Request::input("serie");

        $fecha1 = explode("/", $fechaIni);
        $fecha_ini = $fecha1[2] . "-" . $fecha1[1] . "-" . $fecha1[0];

        $fecha2 = explode("/", $fechaFin);
        $fecha_fin = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0];
        $data = Contabilidad::contabilidad_informe($fecha_ini, $fecha_fin, $serie);
        return $data;
    }

//obligatoria clase abstracta de withHea
    public function headings(): array {
        return ['FECHA EMISIÓN', 'SERIE', 'DEL', 'AL', 'FALTANTES', 'PENSIÓN', 'MATRÍCULA', 'DERECHO ADMISIÓN', 'ANTICIPO PENSIÓN', 'ANTICIPO MATRÍCULA', 'MORA', 'TRAMITE', 'TALLER', 'DEVENGADOS', 'TOTAL'];
    }

    //obligatoria clase abstracta de withEvents
    public function registerEvents(): array {
        /*
          $styleArray = [
          'borders' => [
          'outline' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
          'color' => ['argb' => 'FFFF0000'],
          ],
          ],
          ]; */
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:O1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                /* $event->sheet->getStyle('B2:G8')->applyFromArray([
                  'fill' => array(
                  //'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array('rgb' => 'FF0000')
                  ),
                  'borders' => [
                  'outline' => [
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                  'color' => ['argb' => 'FFFF0000'],
                  ],
                  ],
                  ]); */
                $event->sheet->setTitle("CBB_BANCO");
                $event->sheet->getStyle(
                        'A1:O1')->applyFromArray([
                    'borders' => [
                        'outline' => [
                        //'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        //'color' => ['argb' => 'FFFF0000'],
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    //'background' => ['argb' => 'FFFF0000'],
                    ],
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => '3333FF']
                    )
                ]);

                //$event->sheet->autoSize();
                $event->sheet->setAutoFilter('A1:O1');
                //$event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(21);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(10);
                //$event->sheet->setSize('A1', 500, 50);
            },
        ];
    }

}
