<?php

namespace App\Exports;

use App\Contabilidad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use \Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet; //
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContabilidadExport3 implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, WithTitle {

    use Exportable;

    public function collection() {
        $fechaIni = Request::input("fechaInicio");
        $fechaFin = Request::input("fechaFin");
        $serie = Request::input("serie");

        $fecha1 = explode("/", $fechaIni);
        $fecha_ini = $fecha1[2] . "-" . $fecha1[1] . "-" . $fecha1[0];

        $fecha2 = explode("/", $fechaFin);
        $fecha_fin = $fecha2[2] . "-" . $fecha2[1] . "-" . $fecha2[0];
        //$data = Contabilidad::declaracion_sunat($fecha_ini, $fecha_fin, $serie);
        //chinitos
        $data = Contabilidad::declaracion_sunat_osse($fecha_ini, $fecha_fin, $serie);

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
        return ['FECHA DE EMISIÓN', 'SERIE', 'INICIO', 'FIN', 'CANTIDAD', 'FALTANTES', 'GRAVADAS', 'INAFECTAS', 'EXONERADAS', 'GRATUITAS', 'VALOR DE VENTA', 'IGV', 'TOTAL', 'TIPO'];
    }

    //obligatoria clase abstracta de withEvents
    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:T1')->getFont()->setSize(15);
                $event->sheet->setCellValue('A1', 'Declaración para la SUNAT');
                $event->sheet->getDelegate()->mergeCells('A1:N1');
                $event->sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('A1:N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $cellRange = 'A3:N3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->setTitle("DECLARACION SUNAT");
                $event->sheet->getStyle(
                        'A3:N3')->applyFromArray([
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
                $event->sheet->setAutoFilter('A3:N3');
                //$event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(16);
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
