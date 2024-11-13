<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(10);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    public function unread()
    {
        $notifications = auth()->user()
            ->unreadNotifications()
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'],
                    'data' => $notification->data,
                    'created_at' => $notification->created_at
                ];
            });

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read');
    }

    public function destroy($id)
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Notification deleted');
    }

    protected function broadcastNotification($notification)
    {
        broadcast(new NewNotification(
            [
                'id' => $notification->id,
                'type' => $notification->data['type'],
                'data' => $notification->data,
                'created_at' => $notification->created_at
            ],
            Auth::id()
        ))->toOthers();
    }
}
