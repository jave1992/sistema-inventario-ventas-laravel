<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CategoriesTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function array(): array
    {
        return [
            [
                'Categoría de ejemplo 1',
                'Descripción de la categoría de ejemplo 1',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'description',
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFCCCCCC'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ],
            'A1:B1' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setSelectedCell('A1');
            },
        ];
    }
}
