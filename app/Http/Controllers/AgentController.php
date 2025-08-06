<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Agent;

class AgentController extends Controller
{
    /**
     * Display a listing of agents
     */
    public function index()
    {
        $agents = Agent::all()->map(function ($agent) {
            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'phone' => $agent->phone,
                'email' => $agent->email,
                'userId' => $agent->user_id,
                'status' => $agent->status,
                'policies' => $agent->policies_count,
                'performance' => $agent->performance . '%',
                'address' => $agent->address
            ];
        });
        
        return response()->json(['agents' => $agents]);
    }

    /**
     * Store a newly created agent
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:agents,email|max:255',
            'status' => 'required|in:Active,Inactive',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate unique User ID
        $userId = 'AG' . str_pad(Agent::count() + 1, 3, '0', STR_PAD_LEFT);

        $agent = Agent::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'user_id' => $userId,
            'status' => $request->status,
            'policies_count' => 0,
            'performance' => 0.00,
            'address' => $request->address ?? '',
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Agent created successfully!',
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'phone' => $agent->phone,
                'email' => $agent->email,
                'userId' => $agent->user_id,
                'status' => $agent->status,
                'policies' => $agent->policies_count,
                'performance' => $agent->performance . '%',
                'address' => $agent->address
            ]
        ], 201);
    }

    /**
     * Display the specified agent
     */
    public function show($id)
    {
        $agent = Agent::findOrFail($id);
        
        return response()->json(['agent' => [
            'id' => $agent->id,
            'name' => $agent->name,
            'phone' => $agent->phone,
            'email' => $agent->email,
            'userId' => $agent->user_id,
            'status' => $agent->status,
            'policies' => $agent->policies_count,
            'performance' => $agent->performance . '%',
            'address' => $agent->address
        ]]);
    }

    /**
     * Update the specified agent
     */
    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:agents,email,' . $id,
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status,
            'address' => $request->address ?? $agent->address
        ];

        // Only update password if provided and not empty
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $agent->update($updateData);

        return response()->json([
            'message' => 'Agent updated successfully!',
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'phone' => $agent->phone,
                'email' => $agent->email,
                'userId' => $agent->user_id,
                'status' => $agent->status,
                'policies' => $agent->policies_count,
                'performance' => $agent->performance . '%',
                'address' => $agent->address
            ]
        ]);
    }

    /**
     * Remove the specified agent
     */
    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();

        return response()->json([
            'message' => 'Agent deleted successfully!',
            'id' => $id
        ]);
    }
} 