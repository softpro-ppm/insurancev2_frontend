<?php

namespace App\Imports;

use App\Models\Policy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PoliciesImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use Importable, SkipsErrors;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Generate unique Policy Number
        $policyNumber = 'POL' . str_pad(Policy::count() + 1, 3, '0', STR_PAD_LEFT);

        // Parse dates
        $startDate = $this->parseDate($row['start_date']);
        $endDate = $this->parseDate($row['end_date']);

        // Compute revenue: revenue = customerPaid - (premium - payout)
        $premium = (float) ($row['premium'] ?? 0);
        $payout = (float) ($row['payout'] ?? 0);
        $customerPaidAmount = (float) ($row['customer_paid_amount'] ?? 0);
        $revenue = $customerPaidAmount - ($premium - $payout);
        if ($revenue < 0) {
            $revenue = 0;
        }

        // Determine status based on end date
        $status = $endDate->isPast() ? 'Expired' : 'Active';

        return new Policy([
            'policy_number' => $policyNumber,
            'customer_name' => $row['customer_name'] ?? '',
            'phone' => $row['phone'] ?? '',
            'email' => $row['email'] ?? '',
            'policy_type' => $row['policy_type'] ?? 'Motor',
            'vehicle_number' => $row['vehicle_number'] ?? '',
            'vehicle_type' => $row['vehicle_type'] ?? '',
            'company_name' => $row['company_name'] ?? '',
            'insurance_type' => $row['insurance_type'] ?? '',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'revenue' => $revenue,
            'status' => $status,
            'business_type' => $row['business_type'] ?? 'Agent',
            'agent_name' => $row['agent_name'] ?? 'Self',
        ]);
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return Carbon::now();
        }

        // Try different date formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $date);
                if ($parsed) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // If all formats fail, try to parse as Excel date number
        if (is_numeric($date)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            } catch (\Exception $e) {
                // Fallback to current date
            }
        }

        return Carbon::now();
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'policy_type' => 'required|in:Motor,Health,Life,Home,Travel,Business',
            'company_name' => 'required|string|max:255',
            'insurance_type' => 'required|string|max:255',
            'start_date' => 'required',
            'end_date' => 'required',
            'premium' => 'required|numeric|min:0',
            'customer_paid_amount' => 'required|numeric|min:0',
            'payout' => 'nullable|numeric|min:0',
            'vehicle_number' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:50',
            'business_type' => 'nullable|in:Self,Agent',
            'agent_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'customer_name.required' => 'Customer name is required.',
            'phone.required' => 'Phone number is required.',
            'policy_type.required' => 'Policy type is required.',
            'policy_type.in' => 'Policy type must be one of: Motor, Health, Life, Home, Travel, Business.',
            'company_name.required' => 'Insurance company name is required.',
            'insurance_type.required' => 'Insurance type is required.',
            'start_date.required' => 'Start date is required.',
            'end_date.required' => 'End date is required.',
            'premium.required' => 'Premium amount is required.',
            'premium.numeric' => 'Premium must be a number.',
            'customer_paid_amount.required' => 'Customer paid amount is required.',
            'customer_paid_amount.numeric' => 'Customer paid amount must be a number.',
        ];
    }

    /**
     * Batch size for processing
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
