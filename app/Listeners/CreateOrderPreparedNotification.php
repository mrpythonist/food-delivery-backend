<?php

namespace App\Listeners;

use App\Events\OrderPrepared;
use App\Models\Notification;

class CreateOrderPreparedNotification
{
    public function handle(
        OrderPrepared $event
    ): void {

        Notification::create([
            'notifiable_type' => 'App\Models\Customer',
            'notifiable_id' => $event->order->customer_id,
            'title' => 'Order Preparing',
            'message' =>
            'Your order is being prepared.',
        ]);
    }
}