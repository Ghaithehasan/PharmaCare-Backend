<?php

namespace App\Http\Controllers;

use App\Models\SupplierNotification;
use Illuminate\Http\Request;

class SupplierNotificationController extends Controller
{
    public function show_all_notifications()
    {
        $supplier = auth()->user();
        
        // Get notifications with proper ordering and pagination
        $notifications = $supplier->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get unread notifications count
        $unreadCount = $supplier->notifications()
            ->where('is_read', false)
            ->count();

        // Get notifications by type
        $notificationsByType = [
            'order' => $supplier->notifications()
                ->where('notification_type', 'order')
                ->count(),
            'payment' => $supplier->notifications()
                ->where('notification_type', 'payment')
                ->count(),
            'system' => $supplier->notifications()
                ->where('notification_type', 'system')
                ->count(),
        ];

        return view('suppliers.show_notifications', compact(
            'supplier',
            'notifications',
            'unreadCount',
            'notificationsByType'
        ));
    }

    public function markAsRead($id)
    {
        $notification = SupplierNotification::findOrFail($id);
        
        $notification->update(['is_read' => true]);
        $notification->update(['read_at'=> now()]);
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديد الإشعار كمقروء بنجاح',
                'unreadCount' => auth()->user()->notifications()->where('is_read', false)->count()
            ]);
        }
        
        return redirect()->back()->with('success', 'تم تحديد الإشعار كمقروء بنجاح');
    }

    public function markAllAsRead()
    {
        $supplier = auth()->user();
        $supplier->notifications()->where('is_read', false)->update(['read_at' => now()]);
        $supplier->notifications()->where('is_read', false)->update(['is_read' => true]);
        // $supplier
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديد جميع الإشعارات كمقروءة بنجاح',
                'unreadCount' => 0
            ]);
        }
        
        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة بنجاح');
    }



    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $supplier = auth()->user();
        return view('suppliers.notification_details', compact('notification','supplier'));
    }
}
