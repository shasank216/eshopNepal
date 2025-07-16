<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSeen;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // public function list(Request $request)
    // {

    //     $notification_data = Notification::active()->where(['sent_to'=>'customer']);

    //     $notification = $notification_data->with('notificationSeenBy')
    //         ->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);


    //     return [
    //         'total_size' => $notification->total(),
    //         'limit' => (int)$request['limit'],
    //         'offset' => (int)$request['offset'],
    //         'new_notification' => $notification_data->whereDoesntHave('notificationSeenBy')->count(),
    //         'notification' => $notification->items()
    //     ];
    // }

    public function list(Request $request)
    {
        $customer = auth('api')->user();

        if (!$customer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        // Get actual notifications
        $notification_data = Notification::active()
            ->where('sent_to', 'customer');

        $notifications = $notification_data->with('notificationSeenBy')
            ->latest()
            ->get();

        $orders = Order::where('customer_id', $customer->id)
            ->with(['customer', 'seller'])
            ->where('order_status',  'out_for_delivery')
            ->select('id', 'customer_id', 'order_status', 'seller_id', 'created_at')
            ->latest()
            ->get();

        $orderNotifications = collect($orders)->map(function ($order) {
            return [
                'id' => 'order_' . $order->id,
                'seller_id' => $order->seller_id,
                'status' => $order->order_status,
                'seller_name' => $order->seller->f_name . ' ' . $order->seller->l_name ?? 'Unknown',
                'title' => 'New Order Received',
                'description' => 'You have received a new order from customer ' . ($order->customer->name ?? 'Unknown'),
                'image' => null,
                'created_at' => $order->created_at,
                'notification_seen_status' => 0,
                'customer_id' => $order->customer->id ?? 'Unknown',
                'customer_phone' => $order->customer->phone ?? 'Unknown',
            ];
        });

        // Merge & sort by created_at descending
        $merged = collect($notifications)
            ->merge($orderNotifications)
            ->sortByDesc('created_at')
            ->values();

        // Apply manual pagination
        $paginated = $merged->slice(($offset - 1) * $limit, $limit)->values();

        return [
            'total_size' => $merged->count(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'new_notification' => $notification_data->whereDoesntHave('notificationSeenBy')->count(),
            'notification' => $paginated,
        ];
    }

    public function getDeliveryManNotifications(Request $request)
    {
        $deliveryMan = $request->delivery_man ?? null;

        if (!$deliveryMan) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 1);

        // Static / database-stored notifications sent to delivery men
        $notifications = Notification::active()
            ->where('sent_to', 'delivery')
            ->with('notificationSeenBy')
            ->latest()
            ->get();

        // Dynamic delivery assignment notifications
        $assignedOrders = Order::where('delivery_man_id', $deliveryMan->id)
            ->with('customer:id,name,phone')
            ->select('id', 'customer_id', 'delivery_man_id', 'created_at')
            ->latest()
            ->get();

        $orderNotifications = $assignedOrders->map(function ($order) {
            return [
                'id' => 'assigned_order_' . $order->id,
                'title' => 'New Delivery Assigned',
                'description' => 'You have been assigned a delivery for customer ' . (optional($order->customer)->name ?? 'Unknown'),
                'created_at' => $order->created_at,
                'notification_seen_status' => 0,
                'customer_id' => optional($order->customer)->id ?? 'Unknown',
                'customer_phone' => optional($order->customer)->phone ?? 'Unknown',
            ];
        });

        // Merge and sort
        $merged = collect($notifications)
            ->merge($orderNotifications)
            ->sortByDesc('created_at')
            ->values();

        $paginated = $merged->slice(($offset - 1) * $limit, $limit)->values();

        return response()->json([
            'total_size' => $merged->count(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'new_notification' => Notification::active()
                ->where('sent_to', 'delivery')
                ->whereDoesntHave('notificationSeenBy')
                ->count(),
            'notification' => $paginated,
        ]);
    }



    public function notification_seen(Request $request)
    {
        $user = $request->user();
        NotificationSeen::updateOrInsert(['user_id' => $user->id, 'notification_id' => $request->id], [
            'created_at' => Carbon::now(),
        ]);

        $notification_count = Notification::active()
            ->where('sent_to', 'customer')
            ->whereDoesntHave('notificationSeenBy')
            ->count();

        return [
            'notification_count' => $notification_count,
        ];
    }
}
