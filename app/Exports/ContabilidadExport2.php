<?php

namespace App\Exports;

use App\Contabilidad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContabilidadExport2 implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, WithTitle {

    use Exportable;

    protected $total = 0;

    public function collection() {

        $fechaIni = Request::input("fechaInicio");
        $fechaFin = Request::input("fechaFin");
        $serie = Request::input("serie");

        $fecha1 = explode("/", $fechaIni);
        $fecha_ini = $fecha1[2] . "-" . $fecha1[1] . "-" . $fecha1[0];

        $fecha2 = explode("/", $fechaFin);
        $fecha_fin = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0];
        $data = Contabilidad::contabilidad_cbb_detallado($fecha_ini, $fecha_fin, $serie);
        $this->total = count($data);
        return $data;
    }

    public function title(): string {
        return 'Some Text';
    }

//obligatoria clase abstracta de withHea
    public function headings(): array {
        //$sheetArray[] = array();
        /* $sheetArray[] = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
          $sheetArray[] = array('COMPROBANTE', 'FECHA', 'SERIE', 'NUMERO', 'TIPO DOCUMENTO', 'DOCUMENTO DETALLE', 'NUMERO AFECTADO', 'FECHA NUMERO AFECTADO', 'DOCUMENTO',
          'NOMBRE O RAZÓN', 'DIRECCIÓN', 'OPE. GRAVADAS', 'OPE. INAFECTAS', 'OPE. EXONERADAS', 'OPE. GRATUITAS', 'TOTAL VENTA', 'DESCRIPCIÓN', 'CANTIDAD', 'VALOR', 'VENTA');

          return $sheetArray; */
        return ['COMPROBANTE', 'FECHA', 'SERIE', 'NUMERO', 'TIPO DOCUMENTO', 'DOCUMENTO DETALLE', 'NUMERO AFECTADO', 'FECHA NUMERO AFECTADO', 'DOCUMENTO',
            'NOMBRE O RAZÓN', 'DIRECCIÓN', 'OPE. GRAVADAS', 'OPE. INAFECTAS', 'OPE. EXONERADAS', 'OPE. GRATUITAS', 'TOTAL VENTA', 'DESCRIPCIÓN', 'CANTIDAD', 'VALOR', 'VENTA'];
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
                $event->sheet->getDelegate()->getStyle('A1:T1')->getFont()->setSize(15);
                $event->sheet->setCellValue('A1', 'Detalle de Comprobantes del COLEGIO');
                $event->sheet->getDelegate()->mergeCells('A1:T1');
                $event->sheet->getStyle('A1:T1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('A1:T1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $cellRange = 'A3:T3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->setTitle("COMPROBANTES");
                $event->sheet->getStyle(
                        'A3:T3')->applyFromArray([
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

                $event->sheet->getDelegate()->getStyle('I4:I' . $this->total)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                //$event->sheet->autoSize();
                $event->sheet->setAutoFilter('A3:T3');
                //$event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(9);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(11);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(21);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(19);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(50);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(14);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(11);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(10);
                //$event->sheet->setSize('A1', 500, 50);
            },
        ];
    }

    public function model(array $row) {
        
    }

    public function startCell(): string {
        return 'A3';
    }

}
