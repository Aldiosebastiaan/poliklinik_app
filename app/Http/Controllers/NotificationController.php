<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = $this->visibleNotifications();
        $unreadCount = $notifications->whereNull('read_at')->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = $this->visibleNotifications()->whereNull('read_at')->count();

        return response()->json(['count' => $count]);
    }

    private function visibleNotifications(): Collection
    {
        return auth()->user()->notifications()
            ->latest()
            ->get()
            ->reject(fn (object $notification): bool => data_get($notification->data, 'type') === 'pembayaran_baru')
            ->values();
    }
}
