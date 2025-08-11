<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PoliciesTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Sample data row for demonstration
        return [
            [
                'John Doe',
                '+919876543210',
                'john.doe@example.com',
                'Motor',
                'MH12AB1234',
                'Car',
                'National Insurance Co. Ltd.',
                'Comprehensive',
                '2025-01-01',
                '2026-01-01',
                '15000',
                '0',
                '15000',
                'Agent',
                'John Smith'
            ],
            [
                'Jane Smith',
                '+919876543211',
                'jane.smith@example.com',
                'Health',
                '',
                '',
                'Health Insurance Co.',
                'Family Floater',
                '2025-01-01',
                '2026-01-01',
                '8000',
                '0',
                '8000',
                'Self',
                'Self'
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Customer Name*',
            'Phone*',
            'Email',
            'Policy Type*',
            'Vehicle Number',
            'Vehicle Type',
            'Insurance Company*',
            'Insurance Type*',
            'Start Date*',
            'End Date*',
            'Premium*',
            'Payout',
            'Customer Paid Amount*',
            'Business Type',
            'Agent Name'
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Policies Template';
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Customer Name
            'B' => 15, // Phone
            'C' => 25, // Email
            'D' => 15, // Policy Type
            'E' => 18, // Vehicle Number
            'F' => 15, // Vehicle Type
            'G' => 25, // Insurance Company
            'H' => 20, // Insurance Type
            'I' => 15, // Start Date
            'J' => 15, // End Date
            'K' => 12, // Premium
            'L' => 12, // Payout
            'M' => 20, // Customer Paid Amount
            'N' => 15, // Business Type
            'O' => 20, // Agent Name
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style the sample data rows
        $sheet->getStyle('A2:O3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'],
            ],
            'font' => [
                'color' => ['rgb' => '6B7280'],
            ],
        ]);

        // Add borders
        $sheet->getStyle('A1:O3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Add data validation for Policy Type
        $sheet->getDataValidation('D2')->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $sheet->getDataValidation('D2')->setFormula1('"Motor,Health,Life,Home,Travel,Business"');
        $sheet->getDataValidation('D2')->setAllowBlank(false);
        $sheet->getDataValidation('D2')->setShowDropDown(true);
        $sheet->getDataValidation('D2')->setPromptTitle('Policy Type');
        $sheet->getDataValidation('D2')->setPrompt('Please select a policy type from the dropdown');

        // Add data validation for Business Type
        $sheet->getDataValidation('N2')->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $sheet->getDataValidation('N2')->setFormula1('"Self,Agent"');
        $sheet->getDataValidation('N2')->setAllowBlank(true);
        $sheet->getDataValidation('N2')->setShowDropDown(true);
        $sheet->getDataValidation('N2')->setPromptTitle('Business Type');
        $sheet->getDataValidation('N2')->setPrompt('Please select business type from the dropdown');

        // Add instructions row
        $sheet->insertNewRowBefore(1, 3);
        
        // Instructions
        $sheet->setCellValue('A1', 'POLICIES BULK UPLOAD TEMPLATE');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->setCellValue('A2', 'Instructions:');
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '374151'],
            ],
        ]);

        $sheet->setCellValue('A3', '1. Fields marked with * are required. 2. Dates should be in YYYY-MM-DD format. 3. Premium and amounts should be numbers only. 4. For Motor policies, Vehicle Number and Type are required. 5. Business Type can be "Self" or "Agent". 6. Remove sample data rows before uploading.');
        $sheet->mergeCells('A3:O3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Adjust row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(40);
        $sheet->getRowDimension(4)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(20);
        $sheet->getRowDimension(6)->setRowHeight(20);

        // Update column widths for instructions
        $sheet->getColumnDimension('A')->setWidth(30);
    }
}
