<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'required|in:customer,rider',
            'id' => 'required|integer'
        ]);

        $notifications = Notification::query()
            ->where(
                'notifiable_type',
                $request->type === 'customer'
                    ? 'App\Models\Customer'
                    : 'App\Models\Rider'
            )
            ->where(
                'notifiable_id',
                $request->id
            )
            ->latest()
            ->paginate();

        return $notifications;
    }

    public function show(Notification $notification)
    {
        return $notification;
    }

    public function markAsRead(
        Notification $notification
    ) {
        if (!$notification->read_at) {
            $notification->update([
                'read_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    public function unreadCount(Request $request)
    {
        $request->validate([
            'type' => 'required|in:customer,rider',
            'id' => 'required|integer',
        ]);

        $type = $request->type === 'customer'
            ? \App\Models\Customer::class
            : \App\Models\Rider::class;

        $count = Notification::where(
            'notifiable_type',
            $type
        )
            ->where(
                'notifiable_id',
                $request->id
            )
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->validate([
            'type' => 'required|in:customer,rider',
            'id' => 'required|integer',
        ]);

        $type = $request->type === 'customer'
            ? \App\Models\Customer::class
            : \App\Models\Rider::class;

        Notification::where(
            'notifiable_type',
            $type
        )
            ->where(
                'notifiable_id',
                $request->id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }

    public function destroy(
        Notification $notification
    ) {
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted'
        ]);
    }
}
