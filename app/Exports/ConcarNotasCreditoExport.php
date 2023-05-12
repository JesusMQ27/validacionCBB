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

class ConcarNotasCreditoExport implements FromCollection, /* WithHeadings, */ WithCustomStartCell, WithEvents {

    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {

        $per_id = auth()->user()->id;

        $data = Contabilidad::lista_concar_notas_credito($per_id);
        return $data;
    }

//obligatoria clase abstracta de withEvents
    public function registerEvents(): array {

        return [
            AfterSheet::class => function(AfterSheet $event) {
                //$anio = Request::input('anioDeve');
                $cellRange = "";

                $cellRange = 'A1:AO1';
                //$titulo = 'LISTA DE DEUDORES (DEVENGADOS) - ' . $anio;
                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                //$event->sheet->setCellValue('A1', $titulo);
                //$event->sheet->getDelegate()->mergeCells($cellRange);
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => '000000']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle("A1:AO1")->getAlignment()->setWrapText(true);
                $event->sheet->getStyle("A2:AO2")->getAlignment()->setWrapText(true);

                // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                $event->sheet->getStyle(
                        $cellRange)->applyFromArray([
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

                $event->sheet->getStyle("B4:B" . $event->sheet->getHighestRow())->applyFromArray([
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrap' => TRUE
                    )
                ]);


                $event->sheet->getStyle("K4:K" . $event->sheet->getHighestRow())->applyFromArray([
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrap' => TRUE
                    )
                ]);
                $event->sheet->getStyle("L4:L" . $event->sheet->getHighestRow())->applyFromArray([
                    'alignment' => array(
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrap' => TRUE
                    )
                ]);


                $event->sheet->getStyle("A1")->getAlignment()->setWrapText(true);

                $event->sheet->setAutoFilter($cellRange);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(16);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(14);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(22);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(22);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('V')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('W')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('X')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('Z')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AA')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AB')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AC')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AD')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AE')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AF')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AG')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AH')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AI')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('AJ')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('AK')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('AL')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('AM')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('AN')->setWidth(23);
                $event->sheet->getDelegate()->getColumnDimension('AO')->setWidth(23);
            },
        ];
    }

    public function startCell(): string {
        return "A1";
    }

}
