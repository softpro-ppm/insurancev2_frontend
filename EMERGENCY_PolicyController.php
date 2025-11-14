<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;

class PolicyController extends Controller
{
    public function index()
    {
        try {
            $policies = Policy::all()->map(function($policy) {
                return [
                    "id" => $policy->id,
                    "customerName" => $policy->customer_name,
                    "phone" => $policy->phone,
                    "email" => $policy->email,
                    "policyType" => $policy->policy_type,
                    "vehicleNumber" => $policy->vehicle_number,
                    "vehicleType" => $policy->vehicle_type,
                    "companyName" => $policy->company_name,
                    "insuranceType" => $policy->insurance_type,
                    "startDate" => $policy->start_date ? $policy->start_date->format("d-m-Y") : "",
                    "endDate" => $policy->end_date ? $policy->end_date->format("d-m-Y") : "",
                    "premium" => $policy->premium,
                    "payout" => $policy->payout,
                    "customerPaidAmount" => $policy->customer_paid_amount,
                    "revenue" => $policy->revenue,
                    "status" => $policy->status,
                    "businessType" => $policy->business_type,
                    "agentName" => $policy->agent_name,
                    "createdAt" => $policy->created_at ? $policy->created_at->format("d-m-Y") : "",
                    "policy_copy_path" => $policy->policy_copy_path,
                    "rc_copy_path" => $policy->rc_copy_path,
                    "aadhar_copy_path" => $policy->aadhar_copy_path,
                    "pan_copy_path" => $policy->pan_copy_path,
                    "hasRenewal" => false
                ];
            });
            
            return response()->json(["policies" => $policies]);
        } catch (\Exception $e) {
            return response()->json(["policies" => []]);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $policy = Policy::create($request->all());
            return response()->json(["message" => "Policy created successfully", "policy" => $policy]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error creating policy"], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            return response()->json(["policy" => $policy]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Policy not found"], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->update($request->all());
            return response()->json(["message" => "Policy updated successfully", "policy" => $policy]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error updating policy"], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->delete();
            return response()->json(["message" => "Policy deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error deleting policy"], 500);
        }
    }
}