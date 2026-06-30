<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Services\NotificationService;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Zobrazenie všetkých admin notifikácií
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_admin', true)
            ->notExpired()
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = $this->notificationService->getUnreadCount(Auth::id(), true);

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'has_more' => $notifications->hasMorePages()
            ]);
        }

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * API endpoint pre získanie admin notifikácií (pre dropdown)
     */
    public function getNotifications(Request $request)
    {
        $limit = $request->get('limit', 10);

        $notifications = $this->notificationService->getRecentNotifications(
            Auth::id(), 
            true, 
            $limit
        );

        $unreadCount = $this->notificationService->getUnreadCount(Auth::id(), true);

        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'is_read' => $notification->is_read,
                    'priority' => $notification->priority,
                    'time_ago' => $notification->time_ago,
                    'created_at' => $notification->created_at->toISOString()
                ];
            }),
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Označenie notifikácie ako prečítanej
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('is_admin', true)
            ->firstOrFail();

        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikácia označená ako prečítaná'
            ]);
        }

        return redirect()->back()->with('success', 'Notifikácia označená ako prečítaná');
    }

    /**
     * Označenie všetkých notifikácií ako prečítaných
     */
    public function markAllAsRead(Request $request)
    {
        $count = $this->notificationService->markAllAsRead(Auth::id(), true);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Označených {$count} notifikácií ako prečítaných",
                'marked_count' => $count
            ]);
        }

        return redirect()->back()->with('success', "Označených {$count} notifikácií ako prečítaných");
    }

    /**
     * Vymazanie notifikácie
     */
    public function delete($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('is_admin', true)
            ->firstOrFail();

        $notification->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikácia vymazaná'
            ]);
        }

        return redirect()->back()->with('success', 'Notifikácia vymazaná');
    }

    /**
     * Vymazanie všetkých prečítaných notifikácií
     */
    public function deleteRead(Request $request)
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_admin', true)
            ->where('is_read', true)
            ->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Vymazaných {$count} prečítaných notifikácií",
                'deleted_count' => $count
            ]);
        }

        return redirect()->back()->with('success', "Vymazaných {$count} prečítaných notifikácií");
    }

    /**
     * Získanie počtu neprečítaných notifikácií
     */
    public function getUnreadCount(Request $request)
    {
        $count = $this->notificationService->getUnreadCount(Auth::id(), true);

        return response()->json([
            'unread_count' => $count
        ]);
    }
} 