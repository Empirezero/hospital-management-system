<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
        // ❌ Removed: Auth::user()->unreadNotifications->markAsRead();
        // Marking as read here would wipe unread status before the view renders,
        // preventing you from highlighting unread items in the UI.
        // Handle marking as read via markAllRead() or markRead() instead.
    }

    public function markRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('message', 'All notifications marked as read.');
    }
}
