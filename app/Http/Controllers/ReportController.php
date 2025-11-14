<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Report;

class ReportController extends Controller
{
    /**
     * Display a listing of reports
     */
    public function index()
    {
        $reports = Report::all()->map(function ($report) {
            return [
                'id' => $report->id,
                'title' => $report->title,
                'type' => $report->type,
                'description' => $report->description,
                'generatedBy' => $report->generated_by,
                'generatedDate' => $report->generated_date->format('Y-m-d'),
                'status' => $report->status,
                'filePath' => $report->file_path,
                'createdAt' => $report->created_at->format('Y-m-d')
            ];
        });
        
        return response()->json(['reports' => $reports]);
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'generatedBy' => 'required|string|max:255',
            'generatedDate' => 'required|date',
            'status' => 'required|in:Draft,Generated,Archived',
            'filePath' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = Report::create([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'generated_by' => $request->generatedBy,
            'generated_date' => $request->generatedDate,
            'status' => $request->status,
            'file_path' => $request->filePath,
        ]);

        return response()->json([
            'message' => 'Report created successfully!',
            'report' => [
                'id' => $report->id,
                'title' => $report->title,
                'type' => $report->type,
                'description' => $report->description,
                'generatedBy' => $report->generated_by,
                'generatedDate' => $report->generated_date->format('Y-m-d'),
                'status' => $report->status,
                'filePath' => $report->file_path,
                'createdAt' => $report->created_at->format('Y-m-d')
            ]
        ], 201);
    }

    /**
     * Display the specified report
     */
    public function show($id)
    {
        $report = Report::findOrFail($id);
        
        return response()->json(['report' => [
            'id' => $report->id,
            'title' => $report->title,
            'type' => $report->type,
            'description' => $report->description,
            'generatedBy' => $report->generated_by,
            'generatedDate' => $report->generated_date->format('Y-m-d'),
            'status' => $report->status,
            'filePath' => $report->file_path,
            'createdAt' => $report->created_at->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified report
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'generatedBy' => 'required|string|max:255',
            'generatedDate' => 'required|date',
            'status' => 'required|in:Draft,Generated,Archived',
            'filePath' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = Report::findOrFail($id);
        $report->update([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'generated_by' => $request->generatedBy,
            'generated_date' => $request->generatedDate,
            'status' => $request->status,
            'file_path' => $request->filePath,
        ]);

        return response()->json([
            'message' => 'Report updated successfully!',
            'report' => [
                'id' => $report->id,
                'title' => $report->title,
                'type' => $report->type,
                'description' => $report->description,
                'generatedBy' => $report->generated_by,
                'generatedDate' => $report->generated_date->format('Y-m-d'),
                'status' => $report->status,
                'filePath' => $report->file_path,
                'createdAt' => $report->created_at->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Remove the specified report
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return response()->json([
            'message' => 'Report deleted successfully!',
            'id' => $id
        ]);
    }
} 