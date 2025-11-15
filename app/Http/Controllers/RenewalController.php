<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Policy;
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
     * Get renewal summary stats based on latest policy period end dates.
     *
     * Current behaviour:
     * - For each policy, we consider the latest finished period:
     *   - If the policy has versions: use latest version's end_date.
     *   - If there are no versions: use the main policy's end_date.
     * - A policy belongs to the "current month" bucket if that latest period end_date
     *   falls between the first and last day of the current month.
     * - "Completed" renewals are those policies that have at least one version
     *   (i.e. a renewal has been performed for the latest finished period).
     * - "Pending" renewals are those policies in the month bucket without versions.
     */
    public function summary(Request $request)
    {
        $timePeriod = $request->get('time_period', 'current_month');

        // Currently we only support current_month explicitly; other values can be added later.
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $policies = Policy::with('versions')->get();

        $total = 0;
        $completed = 0;
        $pending = 0;

        foreach ($policies as $policy) {
            // Latest version (highest version_number) represents the last finished period
            $latestVersion = $policy->versions->sortByDesc('version_number')->first();

            if ($latestVersion) {
                $periodEnd = $latestVersion->end_date;
                $hasRenewal = true;
            } else {
                $periodEnd = $policy->end_date;
                $hasRenewal = false;
            }

            if ($periodEnd instanceof Carbon && $periodEnd->between($start, $end)) {
                $total++;
                if ($hasRenewal) {
                    $completed++;
                } else {
                    $pending++;
                }
            }
        }

        return response()->json([
            'time_period' => $timePeriod,
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'totals' => [
                'total' => $total,
                'completed' => $completed,
                'pending' => $pending,
            ],
        ]);
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