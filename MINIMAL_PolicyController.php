<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;

class PolicyController extends Controller
{
    public function index()
    {
        try {
            $policies = Policy::all();
            return response()->json(["policies" => $policies]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $policy = Policy::create($request->all());
            return response()->json(["message" => "Policy created successfully", "policy" => $policy]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            return response()->json(["policy" => $policy]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->update($request->all());
            return response()->json(["message" => "Policy updated successfully", "policy" => $policy]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $policy = Policy::findOrFail($id);
            $policy->delete();
            return response()->json(["message" => "Policy deleted successfully"]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
}