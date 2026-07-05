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
            'type' => 'nullable|in:customer,rider',
            'id' => 'nullable|integer',
            'unread_only' => 'nullable|boolean',
            'per_page' => 'nullable|integer',
        ]);
        $notifications = Notification::query()->with('notifiable');
        if ($request->filled('type') && $request->filled('id')) {
            $notifications->where(
                'notifiable_type',
                $request->type === 'customer'
                    ? 'App\Models\Customer'
                    : 'App\Models\Rider'
            )->where('notifiable_id', $request->id);
        }
        if ($request->boolean('unread_only')) {
            $notifications->whereNull('read_at');
        }
        return $notifications
            ->latest()
            ->paginate($request->integer('per_page', 15));
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
            'type' => 'nullable|in:customer,rider',
            'id' => 'nullable|integer',
        ]);
        $query = Notification::whereNull('read_at');
        if ($request->filled('type') && $request->filled('id')) {
            $type = $request->type === 'customer'
                ? \App\Models\Customer::class
                : \App\Models\Rider::class;
            $query->where('notifiable_type', $type)->where('notifiable_id', $request->id);
        }
        return response()->json([
            'unread_count' => $query->count()
        ]);
    }
    public function markAllAsRead(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:customer,rider',
            'id' => 'nullable|integer',
        ]);
        $query = Notification::whereNull('read_at');
        if ($request->filled('type') && $request->filled('id')) {
            $type = $request->type === 'customer'
                ? \App\Models\Customer::class
                : \App\Models\Rider::class;
            $query->where('notifiable_type', $type)->where('notifiable_id', $request->id);
        }
        $query->update([
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