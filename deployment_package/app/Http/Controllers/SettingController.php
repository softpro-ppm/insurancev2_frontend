<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of settings
     */
    public function index()
    {
        $settings = Setting::all()->map(function ($setting) {
            return [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
                'description' => $setting->description,
                'type' => $setting->type,
                'category' => $setting->category,
                'isActive' => $setting->is_active,
                'createdAt' => $setting->created_at->format('Y-m-d')
            ];
        });
        
        return response()->json(['settings' => $settings]);
    }

    /**
     * Store a newly created setting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'required|string|max:1000',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:string,number,boolean,json',
            'category' => 'required|string|max:100',
            'isActive' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = Setting::create([
            'key' => $request->key,
            'value' => $request->value,
            'description' => $request->description,
            'type' => $request->type,
            'category' => $request->category,
            'is_active' => $request->isActive ?? true,
        ]);

        return response()->json([
            'message' => 'Setting created successfully!',
            'setting' => [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
                'description' => $setting->description,
                'type' => $setting->type,
                'category' => $setting->category,
                'isActive' => $setting->is_active,
                'createdAt' => $setting->created_at->format('Y-m-d')
            ]
        ], 201);
    }

    /**
     * Display the specified setting
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        
        return response()->json(['setting' => [
            'id' => $setting->id,
            'key' => $setting->key,
            'value' => $setting->value,
            'description' => $setting->description,
            'type' => $setting->type,
            'category' => $setting->category,
            'isActive' => $setting->is_active,
            'createdAt' => $setting->created_at->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified setting
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key,' . $id,
            'value' => 'required|string|max:1000',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:string,number,boolean,json',
            'category' => 'required|string|max:100',
            'isActive' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = Setting::findOrFail($id);
        $setting->update([
            'key' => $request->key,
            'value' => $request->value,
            'description' => $request->description,
            'type' => $request->type,
            'category' => $request->category,
            'is_active' => $request->isActive ?? $setting->is_active,
        ]);

        return response()->json([
            'message' => 'Setting updated successfully!',
            'setting' => [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
                'description' => $setting->description,
                'type' => $setting->type,
                'category' => $setting->category,
                'isActive' => $setting->is_active,
                'createdAt' => $setting->created_at->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Remove the specified setting
     */
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        return response()->json([
            'message' => 'Setting deleted successfully!',
            'id' => $id
        ]);
    }
} 