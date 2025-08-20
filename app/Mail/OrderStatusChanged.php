<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    protected $orderId;
    protected $orderStatus;

    /**
     * Create a new message instance.
     */
    public function __construct($orderId, $orderStatus)
    {
        $this->orderId = $orderId;
        $this->orderStatus = $orderStatus;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('Order Status Updated: ') . $this->orderStatus)
                    ->view('email-templates.order-status-changed', [
                        'id' => $this->orderId,
                        'status' => $this->orderStatus
                    ]);
    }
}
