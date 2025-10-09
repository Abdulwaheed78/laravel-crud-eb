<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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

    public function list()
    {
        $notifications = \App\Models\Notification::orderBy('_id','desc')->get();

        return view('notifications.index', compact('notifications'));
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


    public function destroy($id)
    {
        $notification = \App\Models\Notification::find($id);

        if (!$notification) {
            return redirect()->route('notifications.list')->with('error', 'Notification not found.');
        }

        $notification->delete();

        return redirect()->route('notifications.list')->with('success', 'Notification deleted successfully.');
    }


    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('notifications.list')->with('error', 'No notifications selected.');
        }

        \App\Models\Notification::whereIn('_id', $ids)->delete();

        return redirect()->route('notifications.list')->with('success', 'Selected notifications deleted successfully.');
    }


    public function exportCsv()
    {
        $fileName = 'notifications_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $notifications = Notification::orderBy('created_at', 'desc')->get();

        $callback = function () use ($notifications) {
            $handle = fopen('php://output', 'w');

            // Write CSV Header
            fputcsv($handle, [
                'Title',
                'Message',
                'Type',
                'Status',
                'Related Model',
                'Related ID',
                'Created At',
            ]);

            // Write each record
            foreach ($notifications as $n) {
                fputcsv($handle, [
                    $n->title,
                    $n->message,
                    $n->type,
                    $n->is_read ? 'Read' : 'Unread',
                    $n->related_model,
                    $n->related_id,
                    $n->created_at ? $n->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
