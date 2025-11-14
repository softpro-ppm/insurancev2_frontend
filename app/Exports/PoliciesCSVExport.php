<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Agent;

class PoliciesCSVExport implements FromArray, WithHeadings, WithTitle, WithMapping, ShouldAutoSize
{
    /**
     * Get insurance companies by policy type
     */
    private function getInsuranceCompanies($policyType = 'Motor')
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
    private function getInsuranceTypes($policyType = 'Motor')
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
    private function getVehicleTypes()
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

        // Get insurance data for reference
        $motorCompanies = $this->getInsuranceCompanies('Motor');
        $motorTypes = $this->getInsuranceTypes('Motor');
        $healthCompanies = $this->getInsuranceCompanies('Health');
        $healthTypes = $this->getInsuranceTypes('Health');
        $lifeCompanies = $this->getInsuranceCompanies('Life');
        $lifeTypes = $this->getInsuranceTypes('Life');

        // Sample data rows for demonstration - one for each policy type
        return [
            [
                'Motor', // Policy Type
                'Self', // Business Type
                'Rajesh Kumar', // Customer Name
                '9550755039', // Phone
                'rajesh@example.com', // Email
                'MH12AB1234', // Vehicle Number
                'Car', // Vehicle Type
                'ICICI Lombard General Insurance Co. Ltd.', // Insurance Company
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
                'LIC of India', // Insurance Company
                'Term Insurance', // Insurance Type
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
     * @return string
     */
    public function title(): string
    {
        return 'Policies Template';
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return $row;
    }
}
