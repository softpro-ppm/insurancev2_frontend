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
                'createdAt' => $policy->created_at->format('Y-m-d')
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
            'businessType' => 'required|in:Self,Agent1,Agent2',
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:15',
            'customerEmail' => 'nullable|email|max:255',
            'companyName' => 'required|string|max:255',
            'insuranceType' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'premium' => 'required|numeric|min:0',
            'customerPaidAmount' => 'required|numeric|min:0',
            'revenue' => 'required|numeric|min:0',
            'vehicleNumber' => 'nullable|string|max:20',
            'vehicleType' => 'nullable|string|max:50',
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
            'premium' => $request->premium,
            'payout' => $request->payout ?? 0,
            'customer_paid_amount' => $request->customerPaidAmount,
            'revenue' => $request->revenue,
            'status' => 'Active',
            'business_type' => $request->businessType,
            'agent_name' => $request->businessType === 'Self' ? 'Self' : 'Agent ' . substr($request->businessType, -1),
        ]);

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
                'createdAt' => $policy->created_at->format('Y-m-d')
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
            'createdAt' => $policy->created_at->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified policy
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:15',
            'customerEmail' => 'nullable|email|max:255',
            'companyName' => 'required|string|max:255',
            'insuranceType' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'premium' => 'required|numeric|min:0',
            'customerPaidAmount' => 'required|numeric|min:0',
            'revenue' => 'required|numeric|min:0',
            'vehicleNumber' => 'nullable|string|max:20',
            'vehicleType' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policy = Policy::findOrFail($id);
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
            'premium' => $request->premium,
            'payout' => $request->payout ?? $policy->payout,
            'customer_paid_amount' => $request->customerPaidAmount,
            'revenue' => $request->revenue,
            'status' => $request->status ?? $policy->status,
            'business_type' => $request->businessType ?? $policy->business_type,
            'agent_name' => $request->businessType === 'Self' ? 'Self' : 'Agent ' . substr($request->businessType, -1),
        ]);

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
                'createdAt' => $policy->created_at->format('Y-m-d')
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
}