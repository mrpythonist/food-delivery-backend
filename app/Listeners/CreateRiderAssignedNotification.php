<?php

namespace App\Listeners;

use App\Events\RiderAssigned;
use App\Models\Notification;

class CreateRiderAssignedNotification
{
    public function handle(
        RiderAssigned $event
    ): void {

        Notification::create([
            'notifiable_type' => 'App\Models\Customer',
            'notifiable_id' => $event->order->customer_id,
            'title' => 'Rider Assigned',
            'message' =>
            'A rider has been assigned to your order.',
        ]);
    }
}