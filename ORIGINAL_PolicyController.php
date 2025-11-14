<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Policy;
use App\Models\PolicyVersion;
use App\Imports\PoliciesImport;
use App\Exports\PoliciesTemplateExport;
use App\Exports\PoliciesCSVExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PolicyController extends Controller
{
    /**
     * Display a listing of policies
     */
    public function index()
    {
        // Avoid eager-loading heavy relations for listing; fetch minimal fields
        $hasVersionsTable = Schema::hasTable('policy_versions');
        $policies = Policy::select('*')->get()->map(function ($policy) use ($hasVersionsTable) {
            return [
                'id' => $policy->id,
                'customerName' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'policyType' => $policy->policy_type,
                'vehicleNumber' => $policy->vehicle_number,
                'vehicleType' => $policy->vehicle_type,
                'companyName' => $policy->company_name,
                'insuranceType' => $policy->insurance_type,
                'startDate' => $policy->start_date->format('d-m-Y'),
                'endDate' => $policy->end_date->format('d-m-Y'),
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customerPaidAmount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                'businessType' => $policy->business_type,
                'agentName' => $policy->agent_name,
                'createdAt' => $policy->created_at->format('d-m-Y'),
                'policy_copy_path' => $policy->policy_copy_path,
                'rc_copy_path' => $policy->rc_copy_path,
                'aadhar_copy_path' => $policy->aadhar_copy_path,
                'pan_copy_path' => $policy->pan_copy_path,
                'hasRenewal' => $hasVersionsTable ? $policy->versions()->exists() : false,
            ];
        });
        
        return response()->json(['policies' => $policies]);
    }

    // ... rest of the original PolicyController methods would go here
    // For brevity, I'm just including the index method
}