<?php

namespace App\Imports;

use App\Models\Policy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PoliciesImport implements ToModel, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnError, WithStartRow
{
    use Importable, SkipsErrors;

    /**
     * Track the number of rows processed
     */
    private $rowCount = 0;
    /**
     * Track vehicle numbers seen within the current file (canonicalized)
     */
    private $seenCanonicals = [];

    /**
     * Get the number of rows processed
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Skip the header row
     */
    public function startRow(): int
    {
        return 2; // Skip the first row (header)
    }

    /**
     * Prepare data for validation by converting indexed array to associative array
     */
    public function prepareForValidation($data, $index)
    {
        // Convert indexed array to associative array for validation
        return [
            'policy_type' => $data[0] ?? '',
            'business_type' => $data[1] ?? '',
            'customer_name' => $data[2] ?? '',
            'phone' => (string) ($data[3] ?? ''),
            'email' => $data[4] ?? '',
            'vehicle_number' => $data[5] ?? '',
            'vehicle_type' => $data[6] ?? '',
            'company_name' => $data[7] ?? '',
            'insurance_type' => $data[8] ?? '',
            'start_date' => $data[9] ?? '',
            'end_date' => $data[10] ?? '',
            'premium' => $data[11] ?? '',
            'customer_paid_amount' => $data[12] ?? '',
            'payout' => $data[13] ?? '',
            'agent_name' => $data[14] ?? '',
        ];
    }

    /**
     * Skip rows that don't have required data
     */
    public function onRow($row)
    {
        // Debug: Log the row data
        \Log::info('Processing row:', $row);
        
        // Skip empty rows or rows with only headers
        if (empty($row[2]) || // customer_name is at index 2
            $row[2] === 'customer_name' || 
            $row[2] === 'Customer Name*' ||
            strtolower($row[2]) === 'customer name') {
            \Log::info('Skipping header row or empty row');
            return null;
        }
        
        return $this->model($row);
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // After prepareForValidation, rows are associative using our keys
        // Fallbacks handle both associative and indexed arrays

        $get = function ($key, $index = null) use ($row) {
            if (is_array($row) && array_key_exists($key, $row)) {
                return $row[$key];
            }
            if ($index !== null && is_array($row) && array_key_exists($index, $row)) {
                return $row[$index];
            }
            return null;
        };

        // Parse dates
        $startDate = $this->parseDate($get('start_date', 9) ?? '');
        $endDate = $this->parseDate($get('end_date', 10) ?? '');

        // Compute revenue: revenue = customerPaid - (premium - payout)
        $premium = (float) ($get('premium', 11) ?? 0);
        $payout = (float) ($get('payout', 13) ?? 0);
        $customerPaidAmount = (float) ($get('customer_paid_amount', 12) ?? 0);
        $revenue = $customerPaidAmount - ($premium - $payout);
        if ($revenue < 0) {
            $revenue = 0;
        }

        // Determine status based on end date
        $status = $endDate->isPast() ? 'Expired' : 'Active';

        // Determine agent name based on business type
        $businessType = $get('business_type', 1) ?? 'Agent';
        $agentName = $businessType === 'Self' ? 'Self' : ($get('agent_name', 14) ?? 'Agent');

        // Canonicalize and check vehicle number; if duplicate, skip by returning null
        $vehicleNumberRaw = $get('vehicle_number', 5) ?? '';
        $canonical = function ($value) {
            $value = (string) $value;
            $value = strtoupper($value);
            return preg_replace('/[^A-Z0-9]/', '', $value);
        };
        $canonV = $canonical($vehicleNumberRaw);
        if (!empty($canonV)) {
            // Pure PHP duplicate check for broad DB compatibility
            $exists = Policy::get()->contains(function ($p) use ($canonV, $canonical) {
                return $canonical($p->vehicle_number) === $canonV;
            });
            // Duplicate within the uploaded file (only keep first occurrence)
            $seenInFile = isset($this->seenCanonicals[$canonV]);
            if ($exists || $seenInFile) {
                return null; // skip duplicate silently
            }
            // Mark as seen to prevent duplicates within file
            $this->seenCanonicals[$canonV] = true;
        }

        $this->rowCount++;
        return new Policy([
            'customer_name' => $get('customer_name', 2) ?? '',
            'phone' => (string) ($get('phone', 3) ?? ''),
            'email' => $get('email', 4) ?? '',
            'policy_type' => 'Motor', // Force Motor type
            'vehicle_number' => $vehicleNumberRaw,
            'vehicle_type' => $get('vehicle_type', 6) ?? '',
            'company_name' => $get('company_name', 7) ?? '',
            'insurance_type' => $get('insurance_type', 8) ?? '',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'revenue' => $revenue,
            'status' => $status,
            'business_type' => $businessType,
            'agent_name' => $agentName,
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

        // Try different date formats - prioritize dd-mm-yyyy format
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y', 'm-d-Y', 'Y/m/d'];
        
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
            'policy_type' => 'required|in:Motor',
            'business_type' => 'required|in:Self,Agent',
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|max:20', // Remove string validation
            'email' => 'nullable|email|max:255',
            'vehicle_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'company_name' => 'required|string|max:255',
            'insurance_type' => 'required|in:Comprehensive,Stand Alone OD,Third Party',
            'start_date' => 'required',
            'end_date' => 'required',
            'premium' => 'required|numeric|min:0',
            'customer_paid_amount' => 'required|numeric|min:0',
            'payout' => 'nullable|numeric|min:0',
            'agent_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'policy_type.required' => 'Policy type is required.',
            'policy_type.in' => 'Policy type must be Motor.',
            'business_type.required' => 'Business type is required.',
            'business_type.in' => 'Business type must be either "Self" or "Agent".',
            'customer_name.required' => 'Customer name is required.',
            'phone.required' => 'Phone number is required.',
            'vehicle_number.required' => 'Vehicle number is required.',
            'vehicle_type.required' => 'Vehicle type is required.',
            'company_name.required' => 'Insurance company name is required.',
            'insurance_type.required' => 'Insurance type is required.',
            'insurance_type.in' => 'Insurance type must be one of: Comprehensive, Stand Alone OD, Third Party.',
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
