<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Notification;

class CreateOrderPlacedNotification
{
    public function handle(
        OrderPlaced $event
    ): void {

        Notification::create([
            'notifiable_type' => 'App\Models\Customer',
            'notifiable_id' => $event->order->customer_id,
            'title' => 'Order Placed',
            'message' =>
            'Your order has been placed successfully.',
        ]);
    }
}