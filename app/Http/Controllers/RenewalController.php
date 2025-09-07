<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Renewal;

class RenewalController extends Controller
{
    /**
     * Display a listing of renewals
     */
    public function index(Request $request)
    {
        $query = Renewal::query();
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $renewals = $query->orderBy('due_date')->get()->map(function ($renewal) {
            return [
                'id' => $renewal->id,
                'customerName' => $renewal->customer_name,
                'phone' => $renewal->phone,
                'email' => $renewal->email,
                'policyType' => $renewal->policy_type,
                'currentPremium' => $renewal->current_premium,
                'renewalPremium' => $renewal->renewal_premium,
                'dueDate' => optional($renewal->due_date)->format('Y-m-d'),
                'status' => $renewal->status,
                'agentName' => $renewal->agent_name,
                'notes' => $renewal->notes,
                'createdAt' => optional($renewal->created_at)->format('Y-m-d')
            ];
        });
        
        return response()->json(['renewals' => $renewals]);
    }

    /**
     * Store a newly created renewal
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'policyType' => 'required|string|max:50',
            'currentPremium' => 'required|numeric|min:0',
            'renewalPremium' => 'required|numeric|min:0',
            'dueDate' => 'required|date',
            'status' => 'required|in:Pending,Completed,Overdue,Scheduled',
            'agentName' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $renewal = Renewal::create([
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'policy_type' => $request->policyType,
            'current_premium' => $request->currentPremium,
            'renewal_premium' => $request->renewalPremium,
            'due_date' => $request->dueDate,
            'status' => $request->status,
            'agent_name' => $request->agentName,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Renewal created successfully!',
            'renewal' => [
                'id' => $renewal->id,
                'customerName' => $renewal->customer_name,
                'phone' => $renewal->phone,
                'email' => $renewal->email,
                'policyType' => $renewal->policy_type,
                'currentPremium' => $renewal->current_premium,
                'renewalPremium' => $renewal->renewal_premium,
                'dueDate' => $renewal->due_date->format('Y-m-d'),
                'status' => $renewal->status,
                'agentName' => $renewal->agent_name,
                'notes' => $renewal->notes,
                'createdAt' => $renewal->created_at->format('Y-m-d')
            ]
        ], 201);
    }

    /**
     * Display the specified renewal
     */
    public function show($id)
    {
        $renewal = Renewal::findOrFail($id);
        
        return response()->json(['renewal' => [
            'id' => $renewal->id,
            'customerName' => $renewal->customer_name,
            'phone' => $renewal->phone,
            'email' => $renewal->email,
            'policyType' => $renewal->policy_type,
            'currentPremium' => $renewal->current_premium,
            'renewalPremium' => $renewal->renewal_premium,
            'dueDate' => $renewal->due_date->format('Y-m-d'),
            'status' => $renewal->status,
            'agentName' => $renewal->agent_name,
            'notes' => $renewal->notes,
            'createdAt' => $renewal->created_at->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified renewal
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'policyType' => 'required|string|max:50',
            'currentPremium' => 'required|numeric|min:0',
            'renewalPremium' => 'required|numeric|min:0',
            'dueDate' => 'required|date',
            'status' => 'required|in:Pending,Completed,Overdue,Scheduled',
            'agentName' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $renewal = Renewal::findOrFail($id);
        $renewal->update([
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'policy_type' => $request->policyType,
            'current_premium' => $request->currentPremium,
            'renewal_premium' => $request->renewalPremium,
            'due_date' => $request->dueDate,
            'status' => $request->status,
            'agent_name' => $request->agentName,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Renewal updated successfully!',
            'renewal' => [
                'id' => $renewal->id,
                'customerName' => $renewal->customer_name,
                'phone' => $renewal->phone,
                'email' => $renewal->email,
                'policyType' => $renewal->policy_type,
                'currentPremium' => $renewal->current_premium,
                'renewalPremium' => $renewal->renewal_premium,
                'dueDate' => $renewal->due_date->format('Y-m-d'),
                'status' => $renewal->status,
                'agentName' => $renewal->agent_name,
                'notes' => $renewal->notes,
                'createdAt' => $renewal->created_at->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Remove the specified renewal
     */
    public function destroy($id)
    {
        $renewal = Renewal::findOrFail($id);
        $renewal->delete();

        return response()->json([
            'message' => 'Renewal deleted successfully!',
            'id' => $id
        ]);
    }
} 