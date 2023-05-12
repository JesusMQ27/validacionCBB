<?php

namespace App\Exports;

use App\Contabilidad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use \Maatwebsite\Excel\Concerns\Exportable;
//font and size alv
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
//
use Illuminate\Support\Facades\Request;

class DevengadosExportAnio implements FromCollection/* , WithHeadings */, WithCustomStartCell, WithEvents {

    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {

        $anio = Request::input("anioDeve");
        $fecha_fin = Request::input("fecha_fin3");
        $fechita = explode("/", $fecha_fin);
        $nueva_fecha = $fechita[2] . "-" . $fechita[1] . "-" . $fechita[0];
        $data = Contabilidad::lista_devengados_anio($anio, $nueva_fecha);
        return $data;
    }

//obligatoria clase abstracta de withHea
    /* public function headings(): array {
      return ['Fecha de Emision', 'DNI', 'Boleta', 'Alumno', 'Cuota', 'Monto', 'Estado'];
      } */

//obligatoria clase abstracta de withEvents
    public function registerEvents(): array {

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $anio = Request::input('anioDeve');
                $fecha_fin = Request::input("fecha_fin3");
                $titulo = "";
                $cellRange = "";
                $cellRange2 = "";

                $cellRange = 'A1:I1';
                $cellRange2 = 'A3:I3';
                $titulo = 'LISTA DE DEUDORES (DEVENGADOS) HASTA EL ' . $fecha_fin;

                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(15);
                $event->sheet->setCellValue('A1', $titulo);
                $event->sheet->getDelegate()->mergeCells($cellRange);
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange2)->getFont()->setSize(12);
                $event->sheet->getStyle(
                        $cellRange2)->applyFromArray([
                    'borders' => [
                        'outline' => [
                        ],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'fill' => array(
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => '3333FF']
                    )
                ]);
                $event->sheet->setAutoFilter($cellRange2);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
            },
        ];
    }

    public function startCell(): string {
        return "A3";
    }

}
