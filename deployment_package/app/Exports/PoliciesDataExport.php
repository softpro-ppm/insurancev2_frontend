<?php

namespace App\Exports;

use App\Models\Policy;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PoliciesDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Policy::query();

        // Apply filters if provided
        if (!empty($this->filters['policy_type'])) {
            $query->where('policy_type', $this->filters['policy_type']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['start_date'])) {
            $query->where('start_date', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('start_date', '<=', $this->filters['end_date']);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Policy ID',
            'Policy Type',
            'Business Type',
            'Customer Name',
            'Phone',
            'Email',
            'Vehicle Number',
            'Vehicle Type',
            'Insurance Company',
            'Insurance Type',
            'Start Date',
            'End Date',
            'Premium (₹)',
            'Payout (₹)',
            'Customer Paid (₹)',
            'Revenue (₹)',
            'Status',
            'Agent Name',
            'Created Date',
            'Policy Copy',
            'RC Copy',
            'Aadhar Copy',
            'PAN Copy',
            'Notes'
        ];
    }

    /**
     * @param mixed $policy
     * @return array
     */
    public function map($policy): array
    {
        return [
            $policy->id,
            $policy->policy_type,
            $policy->business_type,
            $policy->customer_name,
            $policy->phone,
            $policy->email,
            $policy->vehicle_number,
            $policy->vehicle_type,
            $policy->company_name,
            $policy->insurance_type,
            $policy->start_date ? $policy->start_date->format('d-m-Y') : '',
            $policy->end_date ? $policy->end_date->format('d-m-Y') : '',
            number_format((float) $policy->premium, 0, '.', ','),
            number_format((float) $policy->payout, 0, '.', ','),
            number_format((float) $policy->customer_paid_amount, 0, '.', ','),
            number_format((float) $policy->revenue, 0, '.', ','),
            $policy->status,
            $policy->agent_name,
            $policy->created_at ? $policy->created_at->format('d-m-Y H:i:s') : '',
            $policy->policy_copy_path ? 'Available' : 'Not Available',
            $policy->rc_copy_path ? 'Available' : 'Not Available',
            $policy->aadhar_copy_path ? 'Available' : 'Not Available',
            $policy->pan_copy_path ? 'Available' : 'Not Available',
            $policy->notes ?? ''
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Policies Data Export';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true, 'size' => 12]],
            
            // Set background color for header
            'A1:X1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E8F0']
                ]
            ],
        ];
    }
}
