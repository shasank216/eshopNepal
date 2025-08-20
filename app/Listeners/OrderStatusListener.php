<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\OrderStatusEvent;
use App\Mail\OrderStatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Traits\PushNotificationTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusListener
{
    use PushNotificationTrait;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusEvent $event): void
    {
        $this->sendNotification($event);
        $this->sendMail($event);
    }

    private function sendNotification(OrderStatusEvent $event):void{
        $key = $event->key;
        $type = $event->type;
        $order = $event->order;
        $this->sendOrderNotification(key: $key, type: $type, order: $order);
    }

    private function sendMail(OrderStatusEvent $event): void
    {
        $order = $event->order;

        $customer = User::whereId($order->customer_id)->first();

        if(!$customer){
            \Log::info("message: Customer not found");
            return;
        }

        // Get email from the order object
        $email = $customer->email ?? null;
        $orderId = $order->id;

        $status = ucfirst($order->order_status);


        \Log::info('Order Status Email: '.$order);

        if (!$email) return; // skip if email is not available

        try {
            Mail::to($email)->send(new OrderStatusChanged($orderId, $status));
        } catch (\Exception $e) {
            info('Order Status Email Error: '.$e->getMessage());
        }
    }
}
