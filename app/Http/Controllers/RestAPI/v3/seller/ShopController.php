<?php

namespace App\Http\Controllers\RestAPI\v3\seller;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSeen;
use App\Models\Order;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function vacation_add(Request $request)
    {
        $seller = $request->seller;

        $shop = Shop::where('seller_id', $seller->id)->first();
        $shop->vacation_status = $request->vacation_status;
        $shop->vacation_start_date = $request->vacation_start_date;
        $shop->vacation_end_date = $request->vacation_end_date;
        $shop->vacation_note = $request->vacation_note;
        $shop->save();

        return response()->json(['status' => true], 200);
    }

    public function temporary_close(Request $request)
    {
        $seller = $request->seller;

        $shop = Shop::where('seller_id', $seller->id)->first();
        $shop->temporary_close = $request->status;
        $shop->save();

        return response()->json(['status' => true], 200);
    }


    // public function notification_index(Request $request)
    // {
    //     $seller = $request->seller;

    //     $notification_data = Notification::whereBetween('created_at', [$seller->created_at, Carbon::now()])->where('sent_to', 'seller');

    //     $notification = $notification_data->with('notificationSeenBy')
    //         ->select('id', 'title', 'description', 'image', 'created_at')
    //         ->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

    //     $notification->map(function ($data) {
    //         $data['notification_seen_status'] = $data->notificationSeenBy == null ? 0 : 1;
    //         unset($data->notificationSeenBy);
    //     });

    //     return [
    //         'total_size' => $notification->total(),
    //         'limit' => (int)$request['limit'],
    //         'offset' => (int)$request['offset'],
    //         'new_notification' => $notification_data->whereDoesntHave('notificationSeenBy')->count(),
    //         'notification' => $notification->items()
    //     ];
    // }

    public function notification_index(Request $request)
    {
        $seller = $request->seller;

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 1);

        $notification_data = Notification::whereBetween('created_at', [$seller->created_at, Carbon::now()])
            ->where('sent_to', 'seller');

        $notifications = $notification_data->with('notificationSeenBy')
            ->select('id', 'title', 'description', 'image', 'created_at')
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        $notificationCollection = collect($notifications->map(function ($data) {
            return [
                'id' => $data->id,
                'title' => $data->title,
                'description' => $data->description,
                'image' => $data->image,
                'created_at' => $data->created_at,
                'notification_seen_status' => $data->notificationSeenBy ? 1 : 0,
            ];
        })->all());

        $orders = Order::where('seller_id', $seller->id)
            ->with(['customer', 'seller'])
            ->select('id', 'customer_id','order_status', 'seller_id', 'created_at')
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        $orderNotifications = collect($orders->map(function ($order) {
            return [
                'id' => 'order_' . $order->id,
                'seller_id' =>  $order->seller_id,
                'status' =>  $order->order_status,
                'seller_name' =>  $order->seller->f_name ." ". $order->seller->l_name ?? 'Unknown',
                'title' => 'New Order Received',
                'description' => 'You have received a new order from customer ' . ($order->customer->name ?? 'Unknown'),
                'image' => null,
                'created_at' => $order->created_at,
                'notification_seen_status' => 0,
                'customer_id' => $order->customer->id ?? 'Unknown',
                'customer_phone' => $order->customer->phone ?? 'Unknown',
            ];
        })->all());

        $mergedNotifications = $notificationCollection
            ->merge($orderNotifications)
            ->sortByDesc('created_at')
            ->values();

        return [
            'total_size' => $mergedNotifications->count(),
            'limit' => (int) $limit,
            'offset' => (int) $offset,
            'new_notification' => $notification_data->whereDoesntHave('notificationSeenBy')->count(),
            'notification' => $mergedNotifications,
        ];
    }

    public function seller_notification_view(Request $request)
    {
        $seller = $request->seller;

        NotificationSeen::updateOrInsert(['seller_id' => $seller->id, 'notification_id' => $request->id], [
            'created_at' => Carbon::now(),
        ]);

        $notification_count = Notification::whereBetween('created_at', [$seller->created_at, Carbon::now()])
            ->where('sent_to', 'seller')
            ->whereDoesntHave('notificationSeenBy')
            ->count();

        return [
            'notification_count' => $notification_count,
        ];
    }
}
