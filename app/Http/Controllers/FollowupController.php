<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Followup;

class FollowupController extends Controller
{
    /**
     * Display a listing of followups
     */
    public function index()
    {
        $followups = Followup::orderByDesc('id')->get()->map(function ($followup) {
            return [
                'id' => $followup->id,
                // Optional policy context if available; fallback to nulls
                'policyNumber' => $followup->policy_number ?? null,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                // Frontend expects these names
                'assignedTo' => $followup->agent_name,
                'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
                // Use stored followup_date as the scheduled next follow-up date
                'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
                'recentNote' => Str::limit((string) $followup->notes, 140),
                'notes' => $followup->notes,
                'createdAt' => optional($followup->created_at)->format('Y-m-d')
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
            // Align with frontend payload
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'followupType' => 'required|string|max:100',
            'status' => 'required|string|in:Pending,In Progress,Completed,No Response,Not Interested,Cancelled',
            'assignedTo' => 'nullable|string|max:255',
            'nextFollowupDate' => 'nullable|date',
            'reminderTime' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:2000',
            // Optional fields not persisted directly
            'policyId' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::create([
            // DB requires policy_type; use a sensible default since UI doesn't send
            'policy_type' => $request->input('policyType', 'General'),
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            // Use nextFollowupDate as the scheduled date
            'followup_date' => $request->nextFollowupDate ?? now()->toDateString(),
            'followup_time' => $request->reminderTime ? ($request->reminderTime . (strlen($request->reminderTime) === 5 ? ':00' : '')) : '09:00:00',
            'followup_type' => $request->followupType,
            'status' => $request->status,
            'notes' => $request->notes,
            'agent_name' => $request->assignedTo ?? 'Self',
        ]);

        return response()->json([
            'message' => 'Followup created successfully!',
            'followup' => [
                'id' => $followup->id,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'assignedTo' => $followup->agent_name,
                'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
                'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
                'recentNote' => Str::limit((string) $followup->notes, 140),
                'notes' => $followup->notes,
                'createdAt' => optional($followup->created_at)->format('Y-m-d')
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
            'customerName' => $followup->customer_name,
            'phone' => $followup->phone,
            'email' => $followup->email,
            'followupType' => $followup->followup_type,
            'status' => $followup->status,
            'assignedTo' => $followup->agent_name,
            'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
            'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
            'recentNote' => Str::limit((string) $followup->notes, 140),
            'notes' => $followup->notes,
            'createdAt' => optional($followup->created_at)->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified followup
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customerName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'followupType' => 'required|string|max:100',
            'status' => 'required|string|in:Pending,In Progress,Completed,No Response,Not Interested,Cancelled',
            'assignedTo' => 'nullable|string|max:255',
            'nextFollowupDate' => 'nullable|date',
            'reminderTime' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:2000',
            'policyId' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $followup = Followup::findOrFail($id);
        $followup->update([
            // Keep existing policy_type or set default
            'policy_type' => $followup->policy_type ?? $request->input('policyType', 'General'),
            'customer_name' => $request->customerName,
            'phone' => $request->phone,
            'email' => $request->email,
            'followup_date' => $request->nextFollowupDate ?? $followup->followup_date,
            'followup_time' => $request->reminderTime ? ($request->reminderTime . (strlen($request->reminderTime) === 5 ? ':00' : '')) : $followup->followup_time,
            'followup_type' => $request->followupType,
            'status' => $request->status,
            'notes' => $request->notes,
            'agent_name' => $request->assignedTo ?? $followup->agent_name,
        ]);

        return response()->json([
            'message' => 'Followup updated successfully!',
            'followup' => [
                'id' => $followup->id,
                'customerName' => $followup->customer_name,
                'phone' => $followup->phone,
                'email' => $followup->email,
                'followupType' => $followup->followup_type,
                'status' => $followup->status,
                'assignedTo' => $followup->agent_name,
                'lastFollowupDate' => optional($followup->created_at ?? $followup->followup_date)->format('Y-m-d'),
                'nextFollowupDate' => optional($followup->followup_date)->format('Y-m-d'),
                'recentNote' => Str::limit((string) $followup->notes, 140),
                'notes' => $followup->notes,
                'createdAt' => optional($followup->created_at)->format('Y-m-d')
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