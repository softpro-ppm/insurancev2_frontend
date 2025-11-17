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

class PolicyController extends Controller
{
    /**
     * Display a listing of policies
     */
    public function index(Request $request)
    {
        $policyType = $request->query('policy_type', 'All');
        
        // Build query with optional type filter
        $query = Policy::with('versions');
        
        // Apply policy type filter if specified
        if ($policyType && $policyType !== 'All') {
            $query->where('policy_type', $policyType);
        }
        
        $policies = $query->get()->map(function ($policy) {
            return [
                'id' => $policy->id,
                'customerName' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'customerAge' => $policy->customer_age,
                'customerGender' => $policy->customer_gender,
                'policyType' => $policy->policy_type,
                'vehicleNumber' => $policy->vehicle_number,
                'vehicleType' => $policy->vehicle_type,
                'companyName' => $policy->company_name,
                'insuranceType' => $policy->insurance_type,
                'planType' => $policy->insurance_type, // Use insurance_type as plan_type for now
                'sumInsured' => $policy->sum_insured,
                'sumAssured' => $policy->sum_assured,
                'policyTerm' => $policy->policy_term,
                'premiumFrequency' => $policy->premium_frequency,
                'policyIssueDate' => $policy->policy_issue_date ? $policy->policy_issue_date->format('Y-m-d') : null,
                // Use null-safe formatting to avoid 500s when dates are missing
                'startDate' => $policy->start_date ? $policy->start_date->format('Y-m-d') : null,
                'endDate' => $policy->end_date ? $policy->end_date->format('Y-m-d') : null,
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customerPaidAmount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                'isRenewed' => $policy->is_renewed,
                'businessType' => $policy->business_type,
                'agentName' => $policy->agent_name,
                'createdAt' => $policy->created_at->format('Y-m-d'),
                'policy_copy_path' => $policy->policy_copy_path,
                'rc_copy_path' => $policy->rc_copy_path,
                'aadhar_copy_path' => $policy->aadhar_copy_path,
                'pan_copy_path' => $policy->pan_copy_path,
                'hasRenewal' => $policy->versions()->count() > 0, // Check if policy has been renewed
            ];
        });
        
        // Calculate stats based on filtered policies
        $statsQuery = Policy::query();
        if ($policyType && $policyType !== 'All') {
            $statsQuery->where('policy_type', $policyType);
        }
        
        $stats = [
            'activeCount' => (clone $statsQuery)->where('status', 'Active')->count(),
            'expiredCount' => (clone $statsQuery)->where('status', 'Expired')->count(),
            'pendingCount' => (clone $statsQuery)->where('status', 'Pending')->count(),
            'totalCount' => (clone $statsQuery)->count(),
        ];
        
        return response()->json([
            'policies' => $policies,
            'stats' => $stats,
            'policyType' => $policyType
        ]);
    }

    /**
     * Store a newly created policy
     */
    public function store(Request $request)
    {
        $rules = [
            'policyType' => 'required|in:Motor,Health,Life',
            // Business Type now supports only Self or Agent
            'businessType' => 'required|in:Self,Agent',
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|digits:10',
            'customerEmail' => 'nullable|email|max:255',
            'companyName' => 'required|string|max:255',
            'insuranceType' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'premium' => 'required|numeric|min:0',
            'customerPaidAmount' => 'required|numeric|min:0',
            // revenue is computed server-side
            'payout' => 'nullable|numeric|min:0',
            'vehicleNumber' => 'nullable|string|max:20',
            'vehicleType' => 'nullable|string|max:50',
            // Health/Life Insurance fields
            'customerAge' => 'nullable|integer|min:1|max:120',
            'customerGender' => 'nullable|in:Male,Female,Other',
            'sumInsured' => 'nullable|numeric|min:0',
            'sumAssured' => 'nullable|numeric|min:0',
            'policyTerm' => 'nullable|string|max:50',
            'premiumFrequency' => 'nullable|string|max:50',
            // File upload validation - set to 10MB
            'policyCopy' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max - MANDATORY
            'rcCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'aadharCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'panCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max

        ];

        // Add vehicle validation for Motor policies
        if ($request->policyType === 'Motor') {
            $rules['vehicleNumber'] = 'required|string|max:20';
            $rules['vehicleType'] = 'required|string|max:50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Compute revenue on server: revenue = customerPaid - (premium - payout)
        $premium = (float) $request->premium;
        $payout = (float) ($request->payout ?? 0);
        $customerPaidAmount = (float) $request->customerPaidAmount;
        $computedRevenue = $customerPaidAmount - ($premium - $payout);
        if ($computedRevenue < 0) {
            $computedRevenue = 0; // keep non-negative to align with UI and storage expectations
        }
        
        // Handle policy_issue_date - if not provided, set to start_date - 1 day
        $policyIssueDate = $request->policyIssueDate 
            ? \Carbon\Carbon::parse($request->policyIssueDate)->format('Y-m-d')
            : \Carbon\Carbon::parse($request->startDate)->subDay()->format('Y-m-d');

        // Determine agent name based on business type
        $agentNameResolved = $request->businessType === 'Self' 
            ? 'Self' 
            : ($request->agent_name ?? $request->agentName ?? 'Agent');

        $policy = Policy::create([
            'customer_name' => $request->customerName,
            'phone' => $request->customerPhone,
            'email' => $request->customerEmail,
            'customer_age' => $request->customerAge,
            'customer_gender' => $request->customerGender,
            'policy_type' => $request->policyType,
            'vehicle_number' => $request->vehicleNumber ?? '',
            'vehicle_type' => $request->vehicleType ?? '',
            'company_name' => $request->companyName,
            'insurance_type' => $request->insuranceType,
            'sum_insured' => $request->sumInsured,
            'sum_assured' => $request->sumAssured,
            'policy_term' => $request->policyTerm,
            'premium_frequency' => $request->premiumFrequency,
            'policy_issue_date' => $policyIssueDate,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'revenue' => $computedRevenue,
            'status' => 'Active',
            'is_renewed' => 'No',
            'business_type' => $request->businessType,
            'agent_name' => $agentNameResolved,
        ]);

        // Handle file uploads
        $documentPaths = [];
        
        if ($request->hasFile('policyCopy')) {
            $path = $request->file('policyCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['policy_copy_path'] = $path;
        }
        
        if ($request->hasFile('rcCopy')) {
            $path = $request->file('rcCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['rc_copy_path'] = $path;
        }
        
        if ($request->hasFile('aadharCopy')) {
            $path = $request->file('aadharCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['aadhar_copy_path'] = $path;
        }
        
        if ($request->hasFile('panCopy')) {
            $path = $request->file('panCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['pan_copy_path'] = $path;
        }
        

        
        // Update policy with document paths if any files were uploaded
        if (!empty($documentPaths)) {
            $policy->update($documentPaths);
        }

        // NO initial version creation - policies start fresh
        // Versions are only created when policy is renewed

        return response()->json([
            'message' => 'Policy created successfully!',
            'policy' => [
                'id' => $policy->id,
                'customerName' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'policyType' => $policy->policy_type,
                'vehicleNumber' => $policy->vehicle_number,
                'vehicleType' => $policy->vehicle_type,
                'companyName' => $policy->company_name,
                'insuranceType' => $policy->insurance_type,
                'startDate' => $policy->start_date->format('Y-m-d'),
                'endDate' => $policy->end_date->format('Y-m-d'),
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customerPaidAmount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                'businessType' => $policy->business_type,
                'agentName' => $policy->agent_name,
                'createdAt' => $policy->created_at->format('Y-m-d'),
                'policy_copy_path' => $policy->policy_copy_path,
                'rc_copy_path' => $policy->rc_copy_path,
                'aadhar_copy_path' => $policy->aadhar_copy_path,
            'pan_copy_path' => $policy->pan_copy_path,

        ]
    ], 201);
    }

    /**
     * Display the specified policy
     */
    public function show($id)
    {
        $policy = Policy::findOrFail($id);
        
        return response()->json(['policy' => [
            'id' => $policy->id,
            'customerName' => $policy->customer_name,
            'phone' => $policy->phone,
            'email' => $policy->email,
            'customerAge' => $policy->customer_age,
            'customerGender' => $policy->customer_gender,
            'policyType' => $policy->policy_type,
            'vehicleNumber' => $policy->vehicle_number,
            'vehicleType' => $policy->vehicle_type,
            'companyName' => $policy->company_name,
            'insuranceType' => $policy->insurance_type,
            'planType' => $policy->insurance_type, // Use insurance_type as plan_type for now
            'sumInsured' => $policy->sum_insured,
            'sumAssured' => $policy->sum_assured,
            'policyTerm' => $policy->policy_term,
            'premiumFrequency' => $policy->premium_frequency,
            'policyIssueDate' => $policy->policy_issue_date ? $policy->policy_issue_date->format('Y-m-d') : null,
            'startDate' => $policy->start_date->format('Y-m-d'),
            'endDate' => $policy->end_date->format('Y-m-d'),
            'premium' => $policy->premium,
            'payout' => $policy->payout,
            'customerPaidAmount' => $policy->customer_paid_amount,
            'revenue' => $policy->revenue,
            'status' => $policy->status,
            'isRenewed' => $policy->is_renewed,
            'businessType' => $policy->business_type,
            'agentName' => $policy->agent_name,
            'createdAt' => $policy->created_at->format('Y-m-d'),
            'policy_copy_path' => $policy->policy_copy_path,
            'rc_copy_path' => $policy->rc_copy_path,
            'aadhar_copy_path' => $policy->aadhar_copy_path,
            'pan_copy_path' => $policy->pan_copy_path,
            'versions' => $policy->versions, // Include versions for history display

        ]]);
    }

    /**
     * Update the specified policy
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request data
        \Log::info('Policy update request received', [
            'policy_id' => $id,
            'request_data' => $request->all(),
            'request_headers' => $request->headers->all()
        ]);
        
        // Get the policy to determine its type
        $policy = Policy::findOrFail($id);
        $policyType = $policy->policy_type;
        
        \Log::info('Policy found for update', [
            'policy_id' => $id,
            'policy_type' => $policyType,
            'existing_policy_data' => $policy->toArray()
        ]);
        
        // Base validation rules
        $rules = [
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|digits:10',
            'customerEmail' => 'nullable|email|max:255',
            'companyName' => 'required|string|max:255',
            'insuranceType' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'premium' => 'required|numeric|min:0',
            'customerPaidAmount' => 'required|numeric|min:0',
            // revenue computed server-side
            'payout' => 'nullable|numeric|min:0',
            // Optional business type when updating; restrict to new values
            'businessType' => 'nullable|in:Self,Agent',
            // Health/Life Insurance fields
            'customerAge' => 'nullable|integer|min:1|max:120',
            'customerGender' => 'nullable|in:Male,Female,Other',
            'sumInsured' => 'nullable|numeric|min:0',
            'sumAssured' => 'nullable|numeric|min:0',
            'policyTerm' => 'nullable|string|max:50',
            'premiumFrequency' => 'nullable|string|max:50',
            // File upload validation - set to 10MB
            'policyCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'aadharCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'panCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ];
        
        // Add Motor-specific validation rules only for Motor policies
        if ($policyType === 'Motor') {
            $rules['vehicleNumber'] = 'required|string|max:20';
            $rules['vehicleType'] = 'required|string|max:50';
            $rules['rcCopy'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'; // 5MB max
        }
        
        // Check if this is a RENEWAL or just an EDIT
        $isRenewal = $request->has('is_renewal') && $request->is_renewal === 'true';
        
        // Make policyCopy MANDATORY for renewals
        if ($isRenewal) {
            $rules['policyCopy'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'; // MANDATORY for renewals
            \Log::info('Renewal detected - policyCopy is now REQUIRED');
        }
        
        \Log::info('Validation rules for policy type', [
            'policy_type' => $policyType,
            'is_renewal' => $isRenewal,
            'rules' => $rules
        ]);
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Policy update validation failed', [
                'policy_id' => $id,
                'is_renewal' => $isRenewal,
                'validation_errors' => $validator->errors()->toArray()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policy = Policy::findOrFail($id);
        
        \Log::info('Update type detection', [
            'is_renewal' => $isRenewal,
            'is_renewal_value' => $request->is_renewal
        ]);

        // Create version history ONLY for renewals
        $version = null;
        if ($isRenewal) {
            \Log::info('Creating policy version for renewal', [
                'policy_id' => $id,
                'old_start_date' => $policy->start_date,
                'old_end_date' => $policy->end_date,
                'old_premium' => $policy->premium
            ]);
            
            $version = PolicyVersion::createFromPolicy(
                $policy, 
                'Policy Renewed',
                auth()->user()->name ?? 'System'
            );
            
            \Log::info('Policy version created', [
                'version_id' => $version->id,
                'version_number' => $version->version_number
            ]);

            $this->snapshotVersionDocuments($version);
        } else {
            \Log::info('Regular edit - no version history created');
        }

        // Normalize dates to Y-m-d before saving (same as store method)
        $startDate = \Carbon\Carbon::parse($request->startDate)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($request->endDate)->format('Y-m-d');
        
        // Handle policy_issue_date
        $policyIssueDate = $request->policyIssueDate 
            ? \Carbon\Carbon::parse($request->policyIssueDate)->format('Y-m-d')
            : \Carbon\Carbon::parse($startDate)->subDay()->format('Y-m-d');

        // Compute revenue on server using incoming values
        $premium = (float) $request->premium;
        $payout = (float) ($request->payout ?? $policy->payout ?? 0);
        $customerPaidAmount = (float) $request->customerPaidAmount;
        $computedRevenue = $customerPaidAmount - ($premium - $payout);
        if ($computedRevenue < 0) {
            $computedRevenue = 0;
        }
        
        \Log::info('Policy update calculations', [
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'computed_revenue' => $computedRevenue,
            'policy_issue_date' => $policyIssueDate
        ]);

        // Resolve business type and agent name preserving existing when not provided
        $incomingBusinessType = $request->businessType ?? $policy->business_type;
        $agentNameResolved = $incomingBusinessType === 'Self' 
            ? 'Self' 
            : ($request->agent_name ?? $request->agentName ?? $policy->agent_name ?? 'Agent');

        $policy->update([
            'customer_name' => $request->customerName,
            'phone' => $request->customerPhone,
            'email' => $request->customerEmail,
            'customer_age' => $request->customerAge ?? $policy->customer_age,
            'customer_gender' => $request->customerGender ?? $policy->customer_gender,
            'policy_type' => $request->policyType ?? $policy->policy_type,
            'vehicle_number' => $request->vehicleNumber ?? $policy->vehicle_number,
            'vehicle_type' => $request->vehicleType ?? $policy->vehicle_type,
            'company_name' => $request->companyName,
            'insurance_type' => $request->insuranceType,
            'sum_insured' => $request->sumInsured ?? $policy->sum_insured,
            'sum_assured' => $request->sumAssured ?? $policy->sum_assured,
            'policy_term' => $request->policyTerm ?? $policy->policy_term,
            'premium_frequency' => $request->premiumFrequency ?? $policy->premium_frequency,
            'policy_issue_date' => $policyIssueDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'revenue' => $computedRevenue,
            'status' => $request->status ?? $policy->status,
            'is_renewed' => $isRenewal ? 'Yes' : $policy->is_renewed,
            'business_type' => $incomingBusinessType,
            'agent_name' => $agentNameResolved,
        ]);
        
        \Log::info('Policy updated successfully', [
            'policy_id' => $id,
            'updated_data' => $policy->fresh()->toArray()
        ]);

        // Handle file uploads
        $documentPaths = [];
        
        if ($request->hasFile('policyCopy')) {
            $path = $request->file('policyCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['policy_copy_path'] = $path;
        }
        
        if ($request->hasFile('rcCopy')) {
            $path = $request->file('rcCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['rc_copy_path'] = $path;
        }
        
        if ($request->hasFile('aadharCopy')) {
            $path = $request->file('aadharCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['aadhar_copy_path'] = $path;
        }
        
        if ($request->hasFile('panCopy')) {
            $path = $request->file('panCopy')->store('private/policies/' . $policy->id . '/documents', 'local');
            $documentPaths['pan_copy_path'] = $path;
        }
        

        
        // Update policy with document paths if any files were uploaded or cleared
        if (!empty($documentPaths)) {
            $policy->fill($documentPaths);
            $policy->save();
            $policy->refresh();

            \Log::info('Policy document fields after update', [
                'policy_id' => $policy->id,
                'policy_copy_path' => $policy->policy_copy_path,
                'rc_copy_path' => $policy->rc_copy_path,
                'aadhar_copy_path' => $policy->aadhar_copy_path,
                'pan_copy_path' => $policy->pan_copy_path,
            ]);
        }

        return response()->json([
            'message' => 'Policy updated successfully!',
            'policy' => [
                'id' => $policy->id,
                'customerName' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'policyType' => $policy->policy_type,
                'vehicleNumber' => $policy->vehicle_number,
                'vehicleType' => $policy->vehicle_type,
                'companyName' => $policy->company_name,
                'insuranceType' => $policy->insurance_type,
                'startDate' => $policy->start_date->format('Y-m-d'),
                'endDate' => $policy->end_date->format('Y-m-d'),
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customerPaidAmount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                'businessType' => $policy->business_type,
                'agentName' => $policy->agent_name,
                'createdAt' => $policy->created_at->format('Y-m-d'),
                'policy_copy_path' => $policy->policy_copy_path,
                'rc_copy_path' => $policy->rc_copy_path,
                'aadhar_copy_path' => $policy->aadhar_copy_path,
                'pan_copy_path' => $policy->pan_copy_path,

            ]
        ]);
    }

    private function resolveDocumentPath(?string $filePath): array
    {
        if (!$filePath) {
            return [null, false];
        }

        $trimmedPath = trim($filePath);
        if ($trimmedPath === '') {
            return [null, false];
        }

        if (str_starts_with($trimmedPath, 'http://') || str_starts_with($trimmedPath, 'https://')) {
            return [$trimmedPath, true];
        }

        $candidatePaths = [$trimmedPath, ltrim($trimmedPath, '/')];
        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            foreach ($candidatePaths as $candidate) {
                if ($disk->exists($candidate)) {
                    return [$disk->path($candidate), false];
                }
            }
        }

        $additionalPaths = [
            storage_path('app/' . $trimmedPath),
            storage_path('app/public/' . $trimmedPath),
            storage_path('app/private/' . $trimmedPath),
            public_path('storage/' . $trimmedPath),
            public_path($trimmedPath),
            base_path('public/' . $trimmedPath),
            base_path('storage/app/' . $trimmedPath),
            base_path('storage/app/public/' . $trimmedPath),
            base_path('storage/app/private/' . $trimmedPath),
            $trimmedPath,
        ];

        foreach ($additionalPaths as $path) {
            if ($path && file_exists($path)) {
                return [$path, false];
            }
        }

        return [null, false];
    }

    private function normalizeNameForFilename(?string $name): string
    {
        $name = $name ?? 'document';
        $formatted = strtolower(trim($name));
        $formatted = preg_replace('/[^a-z0-9]+/', '_', $formatted);
        $formatted = trim($formatted, '_');

        return $formatted !== '' ? $formatted : 'document';
    }

    private function detectMimeAndExtension(string $fullPath, ?string $fallbackExtension = null): array
    {
        $mimeType = null;
        if (is_file($fullPath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = finfo_file($finfo, $fullPath) ?: null;
                finfo_close($finfo);
            }
        }

        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        $extension = $mimeToExtension[$mimeType] ?? ($fallbackExtension ?: pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'bin');

        return [$mimeType ?: 'application/octet-stream', $extension];
    }

    private function streamLocalDownload(string $fullPath, string $downloadName, string $mimeType)
    {
        return response()->download($fullPath, $downloadName, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function snapshotVersionDocuments(PolicyVersion $version): void
    {
        $documentFields = [
            'policy_copy_path' => 'policy',
            'rc_copy_path' => 'rc',
            'aadhar_copy_path' => 'aadhar',
            'pan_copy_path' => 'pan',
        ];

        $hasChanges = false;

        foreach ($documentFields as $field => $prefix) {
            $originalPath = $version->$field;

            if (!$originalPath) {
                continue;
            }

            [$fullPath, $isRemote] = $this->resolveDocumentPath($originalPath);

            if (!$fullPath) {
                Log::warning('Snapshot skipped: document not found for policy version', [
                    'version_id' => $version->id,
                    'field' => $field,
                    'stored_path' => $originalPath,
                ]);
                continue;
            }

            if ($isRemote) {
                // Keep remote URLs as-is
                continue;
            }

            $extension = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'bin';
            $snapshotRelativePath = "private/policy_versions/{$version->id}/{$prefix}_v{$version->version_number}.{$extension}";

            try {
                Storage::disk('local')->makeDirectory(dirname($snapshotRelativePath));
            } catch (\Exception $e) {
                Log::warning('Failed to create snapshot directory', [
                    'version_id' => $version->id,
                    'path' => $snapshotRelativePath,
                    'error' => $e->getMessage(),
                ]);
            }

            $targetAbsolutePath = Storage::disk('local')->path($snapshotRelativePath);

            try {
                if (Storage::disk('local')->exists($originalPath)) {
                    // Use Storage copy when the original path is on the same disk
                    Storage::disk('local')->copy($originalPath, $snapshotRelativePath);
                } else {
                    // Fallback to native copy using resolved absolute path
                    if (file_exists($targetAbsolutePath)) {
                        unlink($targetAbsolutePath);
                    }
                    if (!@copy($fullPath, $targetAbsolutePath)) {
                        Log::warning('Failed to copy document to snapshot', [
                            'version_id' => $version->id,
                            'source' => $fullPath,
                            'target' => $targetAbsolutePath,
                        ]);
                        continue;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Snapshot copy failed', [
                    'version_id' => $version->id,
                    'source' => $originalPath,
                    'target' => $snapshotRelativePath,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            $version->$field = $snapshotRelativePath;
            $hasChanges = true;
        }

        if ($hasChanges) {
            $version->save();
        }
    }

    /**
     * Remove the specified policy
     */
    public function destroy($id)
    {
        $policy = Policy::findOrFail($id);
        $policy->delete();

        return response()->json([
            'message' => 'Policy deleted successfully!',
            'id' => $id
        ]);
    }

    /**
     * Download a policy document
     */
    public function downloadDocument($policyId, $documentType)
    {
        $policy = Policy::find($policyId);
        
        if (!$policy) {
            return response()->json(['message' => 'Policy not found'], 404);
        }

        // Map document types to policy fields
        $documentFieldMap = [
            'policy' => 'policy_copy_path',
            'rc' => 'rc_copy_path',
            'aadhar' => 'aadhar_copy_path',
            'pan' => 'pan_copy_path',

        ];

        if (!isset($documentFieldMap[$documentType])) {
            return response()->json(['message' => 'Invalid document type'], 400);
        }

        $documentField = $documentFieldMap[$documentType];
        $filePath = $policy->$documentField;

        if (!$filePath) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        [$fullPath, $isRemote] = $this->resolveDocumentPath($filePath);

        if (!$fullPath) {
            Log::warning('Policy document missing from storage', [
                'policy_id' => $policyId,
                'document_type' => $documentType,
                'stored_path' => $filePath,
            ]);

            return response()->json([
                'message' => 'File not found on server',
            ], 404);
        }

        if ($isRemote) {
            return redirect($fullPath);
        }

        [$mimeType, $extension] = $this->detectMimeAndExtension($fullPath, pathinfo($filePath, PATHINFO_EXTENSION));

        $friendlyNames = [
            'policy' => 'policy_document',
            'rc' => 'RC_document',
            'aadhar' => 'aadhar_document',
            'pan' => 'pan_document',

        ];
        
        $friendlyNameKey = $friendlyNames[$documentType] ?? $documentType . '_document';
        $customerName = $this->normalizeNameForFilename($policy->customer_name ?? 'policy');
        $downloadFileName = sprintf('%s_%s.%s', $customerName, $friendlyNameKey, $extension);
        
        return $this->streamLocalDownload($fullPath, $downloadFileName, $mimeType);
    }

    /**
     * Delete a specific document from a policy and clear its path
     */
    public function deleteDocument($policyId, $documentType)
    {
        $policy = Policy::find($policyId);

        if (!$policy) {
            return response()->json(['message' => 'Policy not found'], 404);
        }

        $documentFieldMap = [
            'policy' => 'policy_copy_path',
            'rc' => 'rc_copy_path',
            'aadhar' => 'aadhar_copy_path',
            'pan' => 'pan_copy_path',
        ];

        if (!isset($documentFieldMap[$documentType])) {
            return response()->json(['message' => 'Invalid document type'], 400);
        }

        $documentField = $documentFieldMap[$documentType];
        $filePath = $policy->$documentField;

        if (!$filePath) {
            return response()->json(['message' => 'Document not found or already removed'], 404);
        }

        try {
            // Delete the file from local storage if present
            Storage::disk('local')->delete($filePath);
        } catch (\Throwable $e) {
            Log::warning('Failed deleting policy document', [
                'policy_id' => $policyId,
                'type' => $documentType,
                'path' => $filePath,
                'error' => $e->getMessage(),
            ]);
        }

        // Clear the document path on the policy
        $policy->$documentField = null;
        $policy->save();

        return response()->json([
            'message' => 'Document removed successfully',
            'policyId' => $policyId,
            'documentType' => $documentType,
        ]);
    }

    /**
     * Download the Excel template for bulk policy upload
     */
    public function downloadTemplate()
    {
        return Excel::download(new PoliciesTemplateExport, 'policies_template.xlsx');
    }

    /**
     * Download the CSV template for bulk policy upload
     */
    public function downloadCSVTemplate()
    {
        return Excel::download(new PoliciesCSVExport, 'policies_template.csv');
    }

    /**
     * Bulk upload policies from Excel file
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max, now supports CSV
        ]);

        try {
            $import = new PoliciesImport();
            
            // Add debugging
            Log::info('Starting bulk upload import');
            
            Excel::import($import, $request->file('excel_file'));
            
            $importedCount = $import->getRowCount();
            
            Log::info('Bulk upload completed successfully', ['imported_count' => $importedCount]);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} policies",
                'imported_count' => $importedCount
            ]);
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            Log::error('Bulk upload validation failed', ['failures' => $failures]);
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values()
                ];
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed for some rows',
                'errors' => $errors
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Bulk policy upload failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview bulk upload file
     */
    public function previewBulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new PoliciesImport();
            
            // Read the file and get data without importing
            $data = Excel::toArray($import, $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in file'
                ], 400);
            }
            
            // The import class already skips the header (startRow(): 2). Do not shift the first row.
            $rows = $data[0];
            
            $validRows = [];
            $invalidRows = [];
            $totalRows = count($rows);

            // Build canonical vehicle numbers from DB for duplicate check
            $canonicalize = function ($value) {
                $value = (string) $value;
                $value = strtoupper($value);
                // Remove spaces, hyphens and non-alphanumerics
                return preg_replace('/[^A-Z0-9]/', '', $value);
            };

            $existingCanonicals = \App\Models\Policy::pluck('vehicle_number')
                ->filter()
                ->map(fn ($v) => $canonicalize($v))
                ->values()
                ->all();
            $existingSet = array_fill_keys($existingCanonicals, true);

            // Track duplicates within the uploaded file (by canonical vehicle number)
            $seenInFile = [];
            
            // Validate each row
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Create associative array from row data supporting both indexed and associative inputs
                $get = function ($key, $index = null) use ($row) {
                    if (is_array($row) && array_key_exists($key, $row)) {
                        return $row[$key];
                    }
                    if ($index !== null && is_array($row) && array_key_exists($index, $row)) {
                        return $row[$index];
                    }
                    return null;
                };

                // Template columns: policy_type[0], business_type[1], customer_name[2], phone[3], email[4], vehicle_number[5], vehicle_type[6], company_name[7], insurance_type[8], start_date[9], end_date[10], premium[11], customer_paid_amount[12], payout[13], agent_name[14]
                $rowData = [
                    'policy_type' => $get('policy_type', 0) ?? '',
                    'business_type' => $get('business_type', 1) ?? '',
                    'customer_name' => $get('customer_name', 2) ?? '',
                    'phone' => (string) ($get('phone', 3) ?? ''),
                    'email' => $get('email', 4) ?? '',
                    'vehicle_number' => $get('vehicle_number', 5) ?? '',
                    'vehicle_type' => $get('vehicle_type', 6) ?? '',
                    'company_name' => $get('company_name', 7) ?? '',
                    'insurance_type' => $get('insurance_type', 8) ?? '',
                    'start_date' => $get('start_date', 9) ?? '',
                    'end_date' => $get('end_date', 10) ?? '',
                    'premium' => $get('premium', 11) ?? '',
                    'customer_paid_amount' => $get('customer_paid_amount', 12) ?? '',
                    'payout' => $get('payout', 13) ?? '',
                    'agent_name' => $get('agent_name', 14) ?? '',
                ];
                
                // Validate the row
                $validator = Validator::make($rowData, $import->rules(), $import->customValidationMessages());

                $errorsList = [];
                if ($validator->fails()) {
                    $errorsList = array_merge($errorsList, $validator->errors()->all());
                }

                // Duplicate checks by vehicle number (canonical)
                $vehicle = $rowData['vehicle_number'] ?? '';
                $canonV = $canonicalize($vehicle);
                if (empty($canonV)) {
                    // Validation already marks required, keep as is
                } else {
                    if (!empty($existingSet[$canonV])) {
                        $errorsList[] = 'Duplicate vehicle number already exists';
                    }
                    // Mark only subsequent occurrences in the same file as duplicate
                    if (!empty($seenInFile[$canonV])) {
                        $errorsList[] = 'Duplicate vehicle number within the uploaded file';
                    }
                    // Record occurrence
                    $seenInFile[$canonV] = true;
                }

                if (!empty($errorsList)) {
                    $invalidRows[] = [
                        'row' => $rowNumber,
                        'policy_type' => $rowData['policy_type'] ?? 'N/A',
                        'customer_name' => $rowData['customer_name'] ?? 'N/A',
                        'phone' => $rowData['phone'] ?? 'N/A',
                        'company_name' => $rowData['company_name'] ?? 'N/A',
                        'errors' => implode(', ', $errorsList)
                    ];
                } else {
                    $validRows[] = [
                        'row' => $rowNumber,
                        'policy_type' => $rowData['policy_type'] ?? 'N/A',
                        'customer_name' => $rowData['customer_name'] ?? 'N/A',
                        'phone' => $rowData['phone'] ?? 'N/A',
                        'company_name' => $rowData['company_name'] ?? 'N/A',
                        'status' => 'Valid'
                    ];
                }
            }
            
            $validCount = count($validRows);
            $invalidCount = count($invalidRows);
            $successRate = $totalRows > 0 ? round(($validCount / $totalRows) * 100, 1) : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_rows' => $totalRows,
                    'valid_rows' => $validCount,
                    'invalid_rows' => $invalidCount,
                    'success_rate' => $successRate,
                    'valid_data' => $validRows,
                    'invalid_data' => $invalidRows
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk upload preview failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get policy version history
     */
    public function getHistory($id)
    {
        $policy = Policy::findOrFail($id);
        $versions = $policy->versions()->with('policy')->get();

        // If no historical versions exist, show only current policy data as a single version
        if ($versions->isEmpty()) {
            $currentDocuments = [];
            if ($policy->policy_copy_path) $currentDocuments['policy_copy'] = $policy->policy_copy_path;
            if ($policy->rc_copy_path) $currentDocuments['rc_copy'] = $policy->rc_copy_path;
            if ($policy->aadhar_copy_path) $currentDocuments['aadhar_copy'] = $policy->aadhar_copy_path;
            if ($policy->pan_copy_path) $currentDocuments['pan_copy'] = $policy->pan_copy_path;
            
            $currentVersion = [
                'id' => 'current_' . $policy->id,
                'version_number' => 1,
                'version_label' => 'Version 1 (' . $policy->updated_at->format('M Y') . ')',
                'policy_period' => $policy->start_date->format('M d, Y') . ' - ' . $policy->end_date->format('M d, Y'),
                'company_name' => $policy->company_name,
                'insurance_type' => $policy->insurance_type,
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customer_paid_amount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                'start_date' => $policy->start_date ? $policy->start_date->format('d-m-Y') : null,
                'end_date' => $policy->end_date ? $policy->end_date->format('d-m-Y') : null,
                'has_documents' => !empty($currentDocuments),
                'documents' => $currentDocuments,
                'notes' => null,
                'created_by' => null,
                'version_created_at' => $policy->updated_at->setTimezone('Asia/Kolkata')->format('M d, Y g:i A'),
            ];

            return response()->json([
                'policy' => [
                    'id' => $policy->id,
                    'customer_name' => $policy->customer_name,
                    'vehicle_number' => $policy->vehicle_number,
                    'policy_type' => $policy->policy_type,
                ],
                'versions' => [$currentVersion]
            ]);
        }

        return response()->json([
            'policy' => [
                'id' => $policy->id,
                'customer_name' => $policy->customer_name,
                'vehicle_number' => $policy->vehicle_number,
                'policy_type' => $policy->policy_type,
            ],
            'versions' => $versions->map(function ($version) {
                return [
                    'id' => $version->id,
                    'version_number' => $version->version_number,
                    'version_label' => $version->version_label,
                    'policy_period' => $version->policy_period,
                    'company_name' => $version->company_name,
                    'insurance_type' => $version->insurance_type,
                    'premium' => $version->premium,
                    'payout' => $version->payout,
                    'customer_paid_amount' => $version->customer_paid_amount,
                    'revenue' => $version->revenue,
                    'status' => $version->status,
                    'start_date' => $version->start_date->format('d-m-Y'),
                    'end_date' => $version->end_date->format('d-m-Y'),
                    'has_documents' => $version->hasDocuments(),
                    'documents' => $version->getDocuments(),
                    'notes' => $version->notes,
                    'created_by' => $version->created_by,
                    'version_created_at' => $version->version_created_at->format('M d, Y g:i A'),
                ];
            })
        ]);
    }

    /**
     * Download document from a specific policy version
     */
    public function downloadVersionDocument($versionId, $documentType)
    {
        $documentFieldMap = [
            'policy' => 'policy_copy_path',
            'rc' => 'rc_copy_path',
            'aadhar' => 'aadhar_copy_path',
            'pan' => 'pan_copy_path',
        ];

        if (!isset($documentFieldMap[$documentType])) {
            return response()->json(['message' => 'Invalid document type'], 400);
        }

        // Handle "current_" prefix for policies without versions
        if (str_starts_with($versionId, 'current_')) {
            $policyId = (int) str_replace('current_', '', $versionId);
            $policy = Policy::find($policyId);

            if (!$policy) {
                return response()->json(['message' => 'Policy not found'], 404);
            }

            $filePath = $policy->{$documentFieldMap[$documentType]};

            if (!$filePath) {
                return response()->json(['message' => 'Document not found for this policy'], 404);
            }

            [$fullPath, $isRemote] = $this->resolveDocumentPath($filePath);

            if (!$fullPath) {
                Log::warning('Current policy document not found on disk', [
                    'policy_id' => $policyId,
                    'document_type' => $documentType,
                    'stored_path' => $filePath,
                ]);

                return response()->json([
                    'message' => 'Document file not found in storage',
                ], 404);
            }

            if ($isRemote) {
                return redirect($fullPath);
            }

            [$mimeType, $extension] = $this->detectMimeAndExtension($fullPath, pathinfo($filePath, PATHINFO_EXTENSION));
            $customerName = $this->normalizeNameForFilename($policy->customer_name ?? 'policy');
            $friendlyName = sprintf('%s_policy_%s.%s', $customerName, $documentType, $extension);

            return $this->streamLocalDownload($fullPath, $friendlyName, $mimeType);
        }

        // Handle regular version IDs
        $version = PolicyVersion::find($versionId);

        if (!$version) {
            return response()->json(['message' => 'Policy version not found'], 404);
        }

        $documentField = $documentFieldMap[$documentType];
        $filePath = $version->$documentField;

        if (!$filePath) {
            return response()->json(['message' => 'Document not found for this version'], 404);
        }

        [$fullPath, $isRemote] = $this->resolveDocumentPath($filePath);

        if (!$fullPath) {
            Log::warning('Version document missing from storage', [
                'version_id' => $versionId,
                'document_type' => $documentType,
                'stored_path' => $filePath,
            ]);

            return response()->json([
                'message' => 'Document file not found in storage',
                'help' => 'Please re-upload the document for this history entry.',
            ], 404);
        }

        if ($isRemote) {
            return redirect($fullPath);
        }

        [$mimeType, $extension] = $this->detectMimeAndExtension($fullPath, pathinfo($filePath, PATHINFO_EXTENSION));
        $customerName = $this->normalizeNameForFilename(
            $version->customer_name ?? optional($version->policy)->customer_name ?? 'policy'
        );

        $friendlyName = sprintf(
            '%s_version_%s_%s.%s',
            $customerName,
            str_pad((string) $version->version_number, 2, '0', STR_PAD_LEFT),
            $documentType,
            $extension
        );

        return $this->streamLocalDownload($fullPath, $friendlyName, $mimeType);
    }

    /**
     * Create a placeholder PDF response for missing documents
     */
    private function createPlaceholderDocumentResponse($record, $documentType)
    {
        $customerName = $record->customer_name;
        $recordType = $record instanceof \App\Models\Policy ? 'Policy' : 'Version';
        $recordId = $record->id;
        
        // Create a simple PDF content (minimal valid PDF)
        $pdfContent = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 300
>>
stream
BT
/F1 16 Tf
72 720 Td
(Document Not Available) Tj
0 -30 Td
/F1 12 Tf
(Customer: {$customerName}) Tj
0 -20 Td
(Document Type: {$documentType}) Tj
0 -20 Td
(Record Type: {$recordType} #{$recordId}) Tj
0 -20 Td
(This document is currently unavailable) Tj
0 -20 Td
(Please contact the administrator) Tj
0 -40 Td
/Date: " . date('Y-m-d H:i:s') . " Tj
ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000274 00000 n 
0000000625 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
707
%%EOF";

        $filename = "{$customerName}_Missing_{$documentType}.pdf";
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdfContent)
        ]);
    }

    /**
     * Preserve documents for a specific version by copying them to version-specific directory
     */
    private function preserveDocumentsForVersion(Policy $policy, PolicyVersion $version)
    {
        $documentFields = ['policy_copy_path', 'rc_copy_path', 'aadhar_copy_path', 'pan_copy_path'];
        
        foreach ($documentFields as $field) {
            $currentPath = $policy->$field;
            if ($currentPath) {
                // Store the current document path in the version
                // This preserves the historical document reference
                $version->update([$field => $currentPath]);
                \Log::info("Document path preserved for version {$version->version_number}: {$field} = {$currentPath}");
            }
        }
    }

    /**
     * Search for existing policies by vehicle number
     */
    public function searchByVehicleNumber($vehicleNumber)
    {
        // Canonicalize the vehicle number for search (remove spaces, convert to uppercase)
        $canonicalVehicleNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vehicleNumber));
        
        // Search for policies with matching vehicle number
        $policies = Policy::all()->filter(function ($policy) use ($canonicalVehicleNumber) {
            $policyVehicleNumber = strtoupper(preg_replace('/[^A-Z0-9]/', '', $policy->vehicle_number));
            return $policyVehicleNumber === $canonicalVehicleNumber;
        })->map(function ($policy) {
            return [
                'id' => $policy->id,
                'customer_name' => $policy->customer_name,
                'phone' => $policy->phone,
                'email' => $policy->email,
                'vehicle_number' => $policy->vehicle_number,
                'vehicle_type' => $policy->vehicle_type,
                'company_name' => $policy->company_name,
                'insurance_type' => $policy->insurance_type,
                'policy_type' => $policy->policy_type,
                'start_date' => $policy->start_date->format('Y-m-d'),
                'end_date' => $policy->end_date->format('Y-m-d'),
                'premium' => $policy->premium,
                'payout' => $policy->payout,
                'customer_paid_amount' => $policy->customer_paid_amount,
                'revenue' => $policy->revenue,
                'status' => $policy->status,
                'business_type' => $policy->business_type,
                'agent_name' => $policy->agent_name,
                'created_at' => $policy->created_at->format('Y-m-d')
            ];
        })->values();

        return response()->json([
            'found' => $policies->count() > 0,
            'count' => $policies->count(),
            'policies' => $policies
        ]);
    }

    /**
     * Export policies data to Excel/CSV
     */
    public function exportPolicies(Request $request)
    {
        // Get filters from request
        $filters = [];
        
        if ($request->has('policy_type') && !empty($request->policy_type)) {
            $filters['policy_type'] = $request->policy_type;
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $filters['status'] = $request->status;
        }
        
        if ($request->has('start_date') && !empty($request->start_date)) {
            $filters['start_date'] = $request->start_date;
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $filters['end_date'] = $request->end_date;
        }

        $format = $request->get('format', 'xlsx'); // Default to Excel
        $filename = 'policies_export_' . date('Y-m-d_H-i-s');
        
        if ($format === 'csv') {
            return Excel::download(new \App\Exports\PoliciesDataExport($filters), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            return Excel::download(new \App\Exports\PoliciesDataExport($filters), $filename . '.xlsx');
        }
    }

    /**
     * Delete a policy version and its documents
     */
    public function deleteVersion($versionId)
    {
        try {
            $version = PolicyVersion::findOrFail($versionId);
            
            \Log::info('Deleting policy version', [
                'version_id' => $versionId,
                'policy_id' => $version->policy_id,
                'version_number' => $version->version_number
            ]);
            
            // Get document paths before deleting
            $documentPaths = [
                $version->policy_copy_path,
                $version->rc_copy_path,
                $version->aadhar_copy_path,
                $version->pan_copy_path,
            ];
            
            // Delete the version record
            $version->delete();
            
            // Attempt to delete physical documents (optional - files may not exist)
            foreach ($documentPaths as $path) {
                if ($path && Storage::exists($path)) {
                    Storage::delete($path);
                    \Log::info('Deleted document file', ['path' => $path]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Policy version deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete policy version', [
                'version_id' => $versionId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete policy version: ' . $e->getMessage()
            ], 500);
        }
    }
}