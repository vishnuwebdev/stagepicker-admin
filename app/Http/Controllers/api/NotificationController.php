<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    /**
     * List notifications for a specific user
     */
    public function index($user_id)
    {
        // Fetch notifications by user_id, newest first
        // $notifications = Notification::where('user_id', $user_id)
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        $notifications = Notification::where('user_id', $user_id)
            ->whereIn(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(custom_data, '$.id')) AS UNSIGNED)"), function ($query) {
                $query->select('id')->from('post_auditions');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'status' => true,
            'message' => 'Notifications fetched successfully.',
            'data' => $notifications,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        // Update only if not already read
        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read successfully.',
            'data' => $notification,
        ]);
    }


    public function store(Request $request)
    {
        // 🧾 Validate request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'ref_id' => 'nullable|string|max:255',
            'is_read' => 'boolean',
            'custom_data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 🗃️ Create notification
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
            'type' => $request->type,
            'ref_id' => $request->ref_id,
            'is_read' => $request->is_read ?? false,
            'custom_data' => $request->custom_data,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notification created successfully',
            'data' => $notification,
        ], 201);
    }
}
