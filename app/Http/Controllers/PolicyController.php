<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Policy;

class PolicyController extends Controller
{
    /**
     * Display a listing of policies
     */
    public function index()
    {
        $policies = Policy::all()->map(function ($policy) {
            return [
                'id' => $policy->id,
                'policyNumber' => $policy->policy_number,
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

            ];
        });
        
        return response()->json(['policies' => $policies]);
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
            // File upload validation - set to 3MB
            'policyCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max
            'rcCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max
            'aadharCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max
            'panCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max

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

        // Generate unique Policy Number
        $policyNumber = 'POL' . str_pad(Policy::count() + 1, 3, '0', STR_PAD_LEFT);

        // Compute revenue on server: revenue = customerPaid - (premium - payout)
        $premium = (float) $request->premium;
        $payout = (float) ($request->payout ?? 0);
        $customerPaidAmount = (float) $request->customerPaidAmount;
        $computedRevenue = $customerPaidAmount - ($premium - $payout);
        if ($computedRevenue < 0) {
            $computedRevenue = 0; // keep non-negative to align with UI and storage expectations
        }

        // Determine agent name based on business type
        $agentNameResolved = $request->businessType === 'Self' 
            ? 'Self' 
            : ($request->agent_name ?? $request->agentName ?? 'Agent');

        $policy = Policy::create([
            'policy_number' => $policyNumber,
            'customer_name' => $request->customerName,
            'phone' => $request->customerPhone,
            'email' => $request->customerEmail,
            'policy_type' => $request->policyType,
            'vehicle_number' => $request->vehicleNumber ?? '',
            'vehicle_type' => $request->vehicleType ?? '',
            'company_name' => $request->companyName,
            'insurance_type' => $request->insuranceType,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'revenue' => $computedRevenue,
            'status' => 'Active',
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

        return response()->json([
            'message' => 'Policy created successfully!',
            'policy' => [
                'id' => $policy->id,
                'policyNumber' => $policy->policy_number,
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
            'policyNumber' => $policy->policy_number,
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
            // File upload validation - set to 3MB
            'policyCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max
            'aadharCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max
            'panCopy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072', // 3MB max
        ];
        
        // Add Motor-specific validation rules only for Motor policies
        if ($policyType === 'Motor') {
            $rules['vehicleNumber'] = 'required|string|max:20';
            $rules['vehicleType'] = 'required|string|max:50';
            $rules['rcCopy'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072'; // 3MB max
        }
        
        \Log::info('Validation rules for policy type', [
            'policy_type' => $policyType,
            'rules' => $rules
        ]);
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Policy update validation failed', [
                'policy_id' => $id,
                'validation_errors' => $validator->errors()->toArray()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policy = Policy::findOrFail($id);

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
            'computed_revenue' => $computedRevenue
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
            'policy_type' => $request->policyType ?? $policy->policy_type,
            'vehicle_number' => $request->vehicleNumber ?? $policy->vehicle_number,
            'vehicle_type' => $request->vehicleType ?? $policy->vehicle_type,
            'company_name' => $request->companyName,
            'insurance_type' => $request->insuranceType,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'premium' => $premium,
            'payout' => $payout,
            'customer_paid_amount' => $customerPaidAmount,
            'revenue' => $computedRevenue,
            'status' => $request->status ?? $policy->status,
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
        

        
        // Update policy with document paths if any files were uploaded
        if (!empty($documentPaths)) {
            $policy->update($documentPaths);
        }

        return response()->json([
            'message' => 'Policy updated successfully!',
            'policy' => [
                'id' => $policy->id,
                'policyNumber' => $policy->policy_number,
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

        // Try multiple possible paths
        $fullPath = storage_path('app/' . $filePath);
        
        if (!file_exists($fullPath)) {
            // Try with 'private/' prefix
            $privatePath = storage_path('app/private/' . $filePath);
            if (file_exists($privatePath)) {
                $fullPath = $privatePath;
            } else {
                // Try replacing 'policies/' with 'private/policies/'
                $privatePath2 = storage_path('app/' . str_replace('policies/', 'private/policies/', $filePath));
                if (file_exists($privatePath2)) {
                    $fullPath = $privatePath2;
                } else {
                    return response()->json(['message' => 'File not found on server'], 404);
                }
            }
        }

        $fileName = basename($filePath);
        
        // Detect the actual file type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);
        
        // Map MIME types to extensions
        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        
        // Get the correct extension based on actual file content
        $fileExtension = $mimeToExtension[$mimeType] ?? 'bin';
        
        // Create a more user-friendly filename with customer name
        $friendlyNames = [
            'policy' => 'policy_document',
            'rc' => 'RC_document',
            'aadhar' => 'aadhar_document',
            'pan' => 'pan_document',

        ];
        
        $friendlyName = $friendlyNames[$documentType] ?? $documentType . '_document';
        
        // Get customer name and format it for filename
        $customerName = $policy->customer_name ?? 'unknown_customer';
        // Clean and format customer name for filename
        $formattedCustomerName = strtolower(trim($customerName));
        // Replace spaces and special characters with underscores
        $formattedCustomerName = preg_replace('/[^a-z0-9]+/', '_', $formattedCustomerName);
        // Remove leading/trailing underscores
        $formattedCustomerName = trim($formattedCustomerName, '_');
        // If empty after cleaning, use default
        if (empty($formattedCustomerName)) {
            $formattedCustomerName = 'unknown_customer';
        }
        
        // Create the final filename: customer_name_document_type.extension
        $downloadFileName = $formattedCustomerName . '_' . $friendlyName . '.' . $fileExtension;
        
        // Set appropriate content type based on detected MIME type
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        
        $contentType = $contentTypes[$fileExtension] ?? 'application/octet-stream';
        
        return response()->download($fullPath, $downloadFileName, [
            'Content-Type' => $contentType
        ]);
    }
}