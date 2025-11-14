<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Agent;

class PoliciesTemplateExport implements FromArray, WithHeadings, WithTitle, WithColumnWidths, WithStyles
{
    /**
     * Get insurance companies by policy type
     */
    public function getInsuranceCompanies($policyType = 'Motor')
    {
        $companies = [
            'Motor' => [
                'The New India',
                'United India',
                'National Insurance',
                'The Oriental',
                'ICICI Lombard',
                'HDFC ERGO',
                'Bajaj Allianz',
                'Tata AIG',
                'Reliance General',
                'SBI General',
                'IFFCO-Tokio',
                'Royal Sundaram',
                'Kotak Mahindra',
                'Chola MS',
                'Shriram General',
                'Universal Sompo',
                'Future Generali',
                'Magma HDI',
                'Raheja QBE',
                'Go Digit',
                'ACKO',
                'Zuno'
            ],
            'Health' => [
                'Star Health and Allied Insurance Co. Ltd.',
                'Niva Bupa Health Insurance Co. Ltd.',
                'Care Health Insurance Ltd.',
                'ManipalCigna Health Insurance Co. Ltd.',
                'Aditya Birla Health Insurance Co. Ltd.'
            ],
            'Life' => [
                'Life Insurance Corporation of India',
                'HDFC Life Insurance Co. Ltd.',
                'ICICI Prudential Life Insurance Co. Ltd.',
                'SBI Life Insurance Co. Ltd.',
                'Max Life Insurance Co. Ltd.',
                'Bajaj Allianz Life Insurance Co. Ltd.',
                'Kotak Mahindra Life Insurance Co. Ltd.',
                'Aditya Birla Sun Life Insurance Co. Ltd.',
                'PNB MetLife India Insurance Co. Ltd.',
                'Tata AIA Life Insurance Co. Ltd.'
            ]
        ];

        return $companies[$policyType] ?? $companies['Motor'];
    }

    /**
     * Get insurance types by policy type
     */
    public function getInsuranceTypes($policyType = 'Motor')
    {
        $types = [
            'Motor' => [
                'Comprehensive',
                'Stand Alone OD',
                'Third Party'
            ],
            'Health' => [
                'Individual',
                'Family Floater',
                'Senior Citizen',
                'Critical Illness'
            ],
            'Life' => [
                'Term Life',
                'Whole Life',
                'Endowment',
                'Money Back',
                'ULIP'
            ]
        ];

        return $types[$policyType] ?? $types['Motor'];
    }

    /**
     * Get vehicle types
     */
    public function getVehicleTypes()
    {
        return [
            'Auto (G)',
            'Auto',
            'Bus',
            'Car (Taxi)',
            'Car',
            'E-Auto',
            'E-Car',
            'HGV',
            'JCB',
            'LCV',
            'Others',
            'Tractor',
            'Trailer',
            '2-Wheeler',
            'Van/Jeep'
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // Get agents from database
        $agents = Agent::pluck('name')->toArray();
        $agentList = array_merge(['Self'], $agents);

        // Sample data rows for demonstration - one for each policy type
        return [
            [
                'Motor', // Policy Type
                'Self', // Business Type
                'Rajesh Kumar', // Customer Name
                '9550755039', // Phone
                'rajesh@example.com', // Email
                'MH12AB1234', // Vehicle Number
                'Car (Private)', // Vehicle Type
                'ICICI Lombard', // Insurance Company
                'Comprehensive', // Insurance Type
                '01-09-2025', // Start Date (dd-mm-yyyy format)
                '01-09-2026', // End Date (dd-mm-yyyy format)
                '15000', // Premium
                '0', // Payout
                '15000', // Customer Paid Amount
                'Self' // Agent Name
            ],
            [
                'Health', // Policy Type
                'Agent', // Business Type
                'Priya Sharma', // Customer Name
                '9876543210', // Phone
                'priya@example.com', // Email
                '', // Vehicle Number (not required for Health)
                '', // Vehicle Type (not required for Health)
                'Star Health and Allied Insurance Co. Ltd.', // Insurance Company
                'Family Floater', // Insurance Type
                '01-09-2025', // Start Date (dd-mm-yyyy format)
                '01-09-2026', // End Date (dd-mm-yyyy format)
                '8000', // Premium
                '0', // Payout
                '8000', // Customer Paid Amount
                'John Smith' // Agent Name
            ],
            [
                'Life', // Policy Type
                'Self', // Business Type
                'Amit Patel', // Customer Name
                '8765432109', // Phone
                'amit@example.com', // Email
                '', // Vehicle Number (not required for Life)
                '', // Vehicle Type (not required for Life)
                'Life Insurance Corporation of India', // Insurance Company
                'Term Life', // Insurance Type
                '01-09-2025', // Start Date (dd-mm-yyyy format)
                '01-09-2026', // End Date (dd-mm-yyyy format)
                '12000', // Premium
                '0', // Payout
                '12000', // Customer Paid Amount
                'Self' // Agent Name
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'policy_type',
            'business_type',
            'customer_name',
            'phone',
            'email',
            'vehicle_number',
            'vehicle_type',
            'company_name',
            'insurance_type',
            'start_date',
            'end_date',
            'premium',
            'customer_paid_amount',
            'payout',
            'agent_name'
        ];
    }

    /**
     * Get user-friendly headers for display
     */
    public function getDisplayHeadings(): array
    {
        return [
            'Policy Type*',
            'Business Type*',
            'Customer Name*',
            'Phone*',
            'Email',
            'Vehicle Number',
            'Vehicle Type',
            'Insurance Company*',
            'Insurance Type*',
            'Start Date*',
            'End Date*',
            'Premium*',
            'Customer Paid Amount*',
            'Payout',
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
            'A' => 15, // Policy Type
            'B' => 15, // Business Type
            'C' => 20, // Customer Name
            'D' => 15, // Phone
            'E' => 25, // Email
            'F' => 18, // Vehicle Number
            'G' => 25, // Vehicle Type
            'H' => 35, // Insurance Company
            'I' => 25, // Insurance Type
            'J' => 15, // Start Date
            'K' => 15, // End Date
            'L' => 12, // Premium
            'M' => 12, // Payout
            'N' => 20, // Customer Paid Amount
            'O' => 20, // Agent Name
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set snake_case headers in the first row (for import compatibility)
        $headings = $this->headings();
        foreach ($headings as $index => $heading) {
            $column = chr(65 + $index); // A, B, C, etc.
            $sheet->setCellValue($column . '1', $heading);
        }

        // Style the header row
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style sample data rows
        $sheet->getStyle('A2:O4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7F3FF'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Add comprehensive instructions and dropdown options
        $this->addInstructionsAndOptions($sheet);

        // Freeze the header row
        $sheet->freezePane('A2');
    }

    /**
     * Add instructions and dropdown options to the sheet
     */
    private function addInstructionsAndOptions(Worksheet $sheet)
    {
        $row = 6;
        
        // Instructions
        $sheet->setCellValue("A{$row}", 'INSTRUCTIONS:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('FF0000'));
        $row++;

        $instructions = [
            '1. Delete the sample data rows (rows 2-4) before adding your data',
            '2. The headers are in technical format (snake_case) for import compatibility',
            '3. Use the dropdown lists below for Policy Type, Business Type, Vehicle Type, Insurance Company, Insurance Type, and Agent Name',
            '4. This template is for MOTOR INSURANCE ONLY',
            '5. Vehicle Number and Vehicle Type are REQUIRED for all Motor policies',
            '6. Date format: DD-MM-YYYY (e.g., 01-09-2025)',
            '7. Phone number: 10 digits only',
            '8. Premium and Customer Paid Amount: Numeric values only',
            '9. Copy and paste values from the dropdown lists below to ensure accuracy'
        ];

        foreach ($instructions as $instruction) {
            $sheet->setCellValue("A{$row}", $instruction);
            $sheet->getStyle("A{$row}")->getFont()->setColor(new Color('FF0000'));
            $row++;
        }

        $row += 2; // Add some space

        // Policy Type Options
        $sheet->setCellValue("A{$row}", 'POLICY TYPE OPTIONS:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $sheet->setCellValue("A{$row}", 'Motor');
        $sheet->setCellValue("B{$row}", 'Health');
        $sheet->setCellValue("C{$row}", 'Life');
        $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row++;

        // Business Type Options
        $sheet->setCellValue("A{$row}", 'BUSINESS TYPE OPTIONS:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $sheet->setCellValue("A{$row}", 'Self');
        $sheet->setCellValue("B{$row}", 'Agent');
        $sheet->getStyle("A{$row}:B{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row++;

        // Vehicle Type Options
        $sheet->setCellValue("A{$row}", 'VEHICLE TYPE OPTIONS (for Motor policies only):');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        
        $vehicleTypes = $this->getVehicleTypes();
        $col = 'A';
        foreach ($vehicleTypes as $index => $type) {
            if ($index > 0 && $index % 5 == 0) {
                $row++;
                $col = 'A';
            }
            $sheet->setCellValue("{$col}{$row}", $type);
            $col++;
        }
        $sheet->getStyle("A{$row}:E" . ($row + ceil(count($vehicleTypes) / 5) - 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row += ceil(count($vehicleTypes) / 5);

        // Motor Insurance Companies
        $sheet->setCellValue("A{$row}", 'MOTOR INSURANCE COMPANIES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        
        $motorCompanies = $this->getInsuranceCompanies('Motor');
        $col = 'A';
        foreach ($motorCompanies as $index => $company) {
            if ($index > 0 && $index % 3 == 0) {
                $row++;
                $col = 'A';
            }
            $sheet->setCellValue("{$col}{$row}", $company);
            $col++;
        }
        $sheet->getStyle("A{$row}:C" . ($row + ceil(count($motorCompanies) / 3) - 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row += ceil(count($motorCompanies) / 3);

        // Motor Insurance Types
        $sheet->setCellValue("A{$row}", 'MOTOR INSURANCE TYPES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $motorTypes = $this->getInsuranceTypes('Motor');
        $col = 'A';
        foreach ($motorTypes as $type) {
            $sheet->setCellValue("{$col}{$row}", $type);
            $col++;
        }
        $sheet->getStyle("A{$row}:C{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row++;

        // Health Insurance Companies
        $sheet->setCellValue("A{$row}", 'HEALTH INSURANCE COMPANIES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $healthCompanies = $this->getInsuranceCompanies('Health');
        $col = 'A';
        foreach ($healthCompanies as $company) {
            $sheet->setCellValue("{$col}{$row}", $company);
            $col++;
        }
        $sheet->getStyle("A{$row}:E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row++;

        // Health Insurance Types
        $sheet->setCellValue("A{$row}", 'HEALTH INSURANCE TYPES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $healthTypes = $this->getInsuranceTypes('Health');
        $col = 'A';
        foreach ($healthTypes as $type) {
            $sheet->setCellValue("{$col}{$row}", $type);
            $col++;
        }
        $sheet->getStyle("A{$row}:D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row++;

        // Life Insurance Companies
        $sheet->setCellValue("A{$row}", 'LIFE INSURANCE COMPANIES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $lifeCompanies = $this->getInsuranceCompanies('Life');
        $col = 'A';
        foreach ($lifeCompanies as $index => $company) {
            if ($index > 0 && $index % 3 == 0) {
                $row++;
                $col = 'A';
            }
            $sheet->setCellValue("{$col}{$row}", $company);
            $col++;
        }
        $sheet->getStyle("A{$row}:C" . ($row + ceil(count($lifeCompanies) / 3) - 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row += ceil(count($lifeCompanies) / 3);

        // Life Insurance Types
        $sheet->setCellValue("A{$row}", 'LIFE INSURANCE TYPES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $lifeTypes = $this->getInsuranceTypes('Life');
        $col = 'A';
        foreach ($lifeTypes as $type) {
            $sheet->setCellValue("{$col}{$row}", $type);
            $col++;
        }
        $sheet->getStyle("A{$row}:E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
        $row++;

        // Agent Names
        $sheet->setCellValue("A{$row}", 'AGENT NAMES:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor(new Color('0066CC'));
        $row++;
        $agents = Agent::pluck('name')->toArray();
        $agentList = array_merge(['Self'], $agents);
        $col = 'A';
        foreach ($agentList as $index => $agent) {
            if ($index > 0 && $index % 4 == 0) {
                $row++;
                $col = 'A';
            }
            $sheet->setCellValue("{$col}{$row}", $agent);
            $col++;
        }
        $sheet->getStyle("A{$row}:D" . ($row + ceil(count($agentList) / 4) - 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6F3FF');
    }
}
