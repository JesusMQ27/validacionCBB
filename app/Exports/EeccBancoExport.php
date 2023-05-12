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

class EeccBancoExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell {

    use Exportable;

    protected $total = 0;

    public function collection() {
        $user = Request::input("user");
        $data = Contabilidad::eecc_lista_reporte("tmp_lista_archivo", $user);
        $this->total = count($data);
        return $data;
    }

    public function title(): string {
        return 'Some Text';
    }

    public function headings(): array {
        return ['SERVICIO', 'USUARIO', 'NOMBRE', 'DOCUMENTO', 'VENCIMIENTO', 'MONEDA', 'IMPORTE', 'MORA', 'FECHA DE PROCESO', 'HORA DE PROCESO', 'FECHA DE PAGO', 'FORMA DE PAGO', 'OFICINA', 'OPERACIÓN', 'REFERENCIA', 'BANCO'];
    }

    public function registerEvents(): array {

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:O1')->getFont()->setSize(15);
                $event->sheet->setCellValue('A1', 'Estado de Cuenta - Bancos');
                $event->sheet->getDelegate()->mergeCells('A1:P1');
                $event->sheet->getStyle('A1:P1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('A1:O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->getStyle('B4:B' . $this->total)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('M4:M' . $this->total)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('N4:N' . $this->total)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                //Jesus M
                
                
                $cellRange = 'A3:P3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(10);
                $event->sheet->setTitle("Lista");
                $event->sheet->getStyle(
                        'A3:P3')->applyFromArray([
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
                $event->sheet->setAutoFilter('A3:P3');
                //$event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(35);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(11);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(21);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(13);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(20);
                //$event->sheet->setSize('A1', 500, 50);
            },
        ];
    }

    public function startCell(): string {
        return 'A3';
    }

}
