<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Get latest unread + read notifications (limit to 10 if you want)
        $notifications = Notification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json($notifications);
    }


    public function markAsRead(Request $request)
    {
        // If specific IDs are sent, mark only those; otherwise mark all unread notifications
        if ($request->filled('ids') && is_array($request->ids)) {
            Notification::whereIn('_id', $request->ids)->update(['is_read' => true]);
        } else {
            Notification::where('is_read', false)->update(['is_read' => true]);
        }

        return response()->json(['status' => 'success']);
    }
}
