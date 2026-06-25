<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Notification;

class CreateOrderConfirmedNotification
{
    public function handle(
        OrderConfirmed $event
    ): void {

        Notification::create([
            'notifiable_type' => 'App\Models\Customer',
            'notifiable_id' => $event->order->customer_id,
            'title' => 'Order Confirmed',
            'message' =>
            'Your order has been confirmed.',
        ]);
    }
}