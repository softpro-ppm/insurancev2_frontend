<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Followup;

class FollowupController extends Controller
{
    /**
     * Display a listing of followups
     */
    public function index()
    {
        $followups = Followup::all()->map(function ($followup) {
            return [
                'id' => $followup->id,
                'policyNumber' => $followup->policy_number,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupDate' => $followup->followup_date->format('Y-m-d'),
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'notes' => $followup->notes,
                'agentName' => $followup->agent_name,
                'createdAt' => $followup->created_at->format('Y-m-d')
            ];
        });
        
        return response()->json(['followups' => $followups]);
    }

    /**
     * Store a newly created followup
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'policyNumber' => 'required|string|max:50',
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'followupDate' => 'required|date',
            'followupType' => 'required|string|max:100',
            'status' => 'required|in:Pending,Completed,Cancelled',
            'notes' => 'nullable|string|max:1000',
            'agentName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::create([
            'policy_number' => $request->policyNumber,
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'followup_date' => $request->followupDate,
            'followup_type' => $request->followupType,
            'status' => $request->status,
            'notes' => $request->notes,
            'agent_name' => $request->agentName,
        ]);

        return response()->json([
            'message' => 'Followup created successfully!',
            'followup' => [
                'id' => $followup->id,
                'policyNumber' => $followup->policy_number,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupDate' => $followup->followup_date->format('Y-m-d'),
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'notes' => $followup->notes,
                'agentName' => $followup->agent_name,
                'createdAt' => $followup->created_at->format('Y-m-d')
            ]
        ], 201);
    }

    /**
     * Display the specified followup
     */
    public function show($id)
    {
        $followup = Followup::findOrFail($id);
        
        return response()->json(['followup' => [
            'id' => $followup->id,
            'policyNumber' => $followup->policy_number,
            'customerName' => $followup->customer_name,
            'phone' => $followup->phone,
            'email' => $followup->email,
            'followupDate' => $followup->followup_date->format('Y-m-d'),
            'followupType' => $followup->followup_type,
            'status' => $followup->status,
            'notes' => $followup->notes,
            'agentName' => $followup->agent_name,
            'createdAt' => $followup->created_at->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified followup
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'policyNumber' => 'required|string|max:50',
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'followupDate' => 'required|date',
            'followupType' => 'required|string|max:100',
            'status' => 'required|in:Pending,Completed,Cancelled',
            'notes' => 'nullable|string|max:1000',
            'agentName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::findOrFail($id);
        $followup->update([
            'policy_number' => $request->policyNumber,
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'followup_date' => $request->followupDate,
            'followup_type' => $request->followupType,
            'status' => $request->status,
            'notes' => $request->notes,
            'agent_name' => $request->agentName,
        ]);

        return response()->json([
            'message' => 'Followup updated successfully!',
            'followup' => [
                'id' => $followup->id,
                'policyNumber' => $followup->policy_number,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupDate' => $followup->followup_date->format('Y-m-d'),
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'notes' => $followup->notes,
                'agentName' => $followup->agent_name,
                'createdAt' => $followup->created_at->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Remove the specified followup
     */
    public function destroy($id)
    {
        $followup = Followup::findOrFail($id);
        $followup->delete();

        return response()->json([
            'message' => 'Followup deleted successfully!',
            'id' => $id
        ]);
    }
} 