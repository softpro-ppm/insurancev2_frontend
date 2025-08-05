<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $notifications = Notification::all()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'recipient' => $notification->recipient,
                'status' => $notification->status,
                'scheduledDate' => $notification->scheduled_date ? $notification->scheduled_date->format('Y-m-d H:i:s') : null,
                'sentDate' => $notification->sent_date ? $notification->sent_date->format('Y-m-d H:i:s') : null,
                'createdAt' => $notification->created_at->format('Y-m-d')
            ];
        });
        
        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:SMS,Email,Push',
            'recipient' => 'required|string|max:255',
            'status' => 'required|in:Pending,Sent,Failed',
            'scheduledDate' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'recipient' => $request->recipient,
            'status' => $request->status,
            'scheduled_date' => $request->scheduledDate,
            'sent_date' => $request->status === 'Sent' ? now() : null,
        ]);

        return response()->json([
            'message' => 'Notification created successfully!',
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'recipient' => $notification->recipient,
                'status' => $notification->status,
                'scheduledDate' => $notification->scheduled_date ? $notification->scheduled_date->format('Y-m-d H:i:s') : null,
                'sentDate' => $notification->sent_date ? $notification->sent_date->format('Y-m-d H:i:s') : null,
                'createdAt' => $notification->created_at->format('Y-m-d')
            ]
        ], 201);
    }

    /**
     * Display the specified notification
     */
    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        return response()->json(['notification' => [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'recipient' => $notification->recipient,
            'status' => $notification->status,
            'scheduledDate' => $notification->scheduled_date ? $notification->scheduled_date->format('Y-m-d H:i:s') : null,
            'sentDate' => $notification->sent_date ? $notification->sent_date->format('Y-m-d H:i:s') : null,
            'createdAt' => $notification->created_at->format('Y-m-d')
        ]]);
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:SMS,Email,Push',
            'recipient' => 'required|string|max:255',
            'status' => 'required|in:Pending,Sent,Failed',
            'scheduledDate' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification = Notification::findOrFail($id);
        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'recipient' => $request->recipient,
            'status' => $request->status,
            'scheduled_date' => $request->scheduledDate,
            'sent_date' => $request->status === 'Sent' ? now() : $notification->sent_date,
        ]);

        return response()->json([
            'message' => 'Notification updated successfully!',
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'recipient' => $notification->recipient,
                'status' => $notification->status,
                'scheduledDate' => $notification->scheduled_date ? $notification->scheduled_date->format('Y-m-d H:i:s') : null,
                'sentDate' => $notification->sent_date ? $notification->sent_date->format('Y-m-d H:i:s') : null,
                'createdAt' => $notification->created_at->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Remove the specified notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully!',
            'id' => $id
        ]);
    }
} 