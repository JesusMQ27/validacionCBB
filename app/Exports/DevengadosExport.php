<?php

namespace App\Exports;

use App\Devengados;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use \Maatwebsite\Excel\Concerns\Exportable;
//font and size alv
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
//
use Illuminate\Support\Facades\Request;

class DevengadosExport implements FromCollection/* , WithHeadings */, WithCustomStartCell, WithEvents {

    use Exportable;

    protected $total = 0; //chinitos

    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection() {
        /* dd(Request::input());
          //        $request = new Request();
          //$fini = $request->input('fini');
          $fini = Request::input('fini'); */
        $data = Devengados::deve_reporte_excel(Request::input());
        $this->total = count($data); //chinitos
        return $data;
    }

//obligatoria clase abstracta de withHea
    /* public function headings(): array {
      return ['Fecha de Emisión', 'DNI', 'Boleta', 'Alumno', 'Cuota', 'Monto', 'Estado'];
      } */

//obligatoria clase abstracta de withEvents
    public function registerEvents(): array {

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $tipo = Request::input('deverptipo');
                $titulo = "";
                $cellRange = "";
                $cellRange2 = "";
                if ($tipo == 1) {//devengado
                    $cellRange = 'A1:H1';
                    $cellRange2 = 'A3:H3';
                    $titulo = 'LISTA DE DEUDORES (DEVENGADOS)';
                } else if ($tipo == 2) {//pago
                    $cellRange = 'A1:K1';
                    $cellRange2 = 'A3:K3';
                    $titulo = 'LISTA DE PAGOS';
                } else if ($tipo == 3) {//anulado
                    $cellRange = 'A1:J1';
                    $cellRange2 = 'A3:J3';
                    $titulo = 'LISTA DE ANULADOS (NOTA DE CREDITO)';
                } else if ($tipo == 4) {//todos los devengados
                    $cellRange = 'A1:I1';
                    $cellRange2 = 'A3:I3';
                    $titulo = 'LISTA TOTAL DE DEVENGADOS';
                } else if ($tipo == 5) { //chinitos
                    $cellRange = 'A1:S1';
                    $cellRange2 = 'A3:S3';
                    $titulo = 'LISTA TOTAL DE COMPROBANTES DECLARADOS EN LA OSE';
                }
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
                //chinitos 
                if ($tipo == 5) {
                    $event->sheet->getDelegate()->getStyle('C4:C' . $this->total)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->getDelegate()->getStyle('A1:S1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
                //chinitos

                $event->sheet->setAutoFilter($cellRange2);
                if ($tipo < 5) {//chinitos
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(12);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(50);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                    if ($tipo == 3) {//anulado
                        $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                        $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(40);
                        $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                        $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
                    } else {
                        $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(60);
                        $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                        $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                        $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
                    }
                } else {
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(9);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(35);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(22);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(17);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(42);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(17);
                    $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(13);
                    $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(13);
                    $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(13);
                    $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(10);
                    $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(10);
                    $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(10);
                    $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(10);
                    $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(12);
                    $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(18);
                    $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(19);
                    $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(12);
                }
            },
        ];
    }

    public function startCell(): string {
        return "A3";
    }

}
