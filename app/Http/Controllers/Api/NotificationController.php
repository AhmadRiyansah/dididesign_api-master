<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Ambil notifikasi milik user.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'body'       => $n->body,
                'type'       => $n->type,
                'is_read'    => (bool) $n->is_read,
                'data'       => $n->data ? json_decode($n->data, true) : null,
                'created_at' => $n->created_at?->diffForHumans(),
            ]);

        $unreadCount = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'data'         => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Semua notifikasi sudah dibaca.']);
    }
}
