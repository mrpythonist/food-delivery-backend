<?php

namespace App\Listeners;

use App\Events\OrderDelivered;
use App\Models\Notification;

class CreateOrderDeliveredNotification
{
    public function handle(
        OrderDelivered $event
    ): void {

        Notification::create([
            'notifiable_type' => 'App\Models\Customer',
            'notifiable_id' => $event->order->customer_id,
            'title' => 'Order Delivered',
            'message' =>
            'Your order has been delivered.',
        ]);
    }
}