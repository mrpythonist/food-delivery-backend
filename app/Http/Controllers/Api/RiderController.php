<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\RiderLocation;
use App\Models\Rider;
use App\Http\Requests\RiderRequest;
use App\Events\RiderAssigned;

class RiderController extends Controller
{
    public function orders(Request $request)
    {
        $riderId = $request->rider_id;

        return Order::with(['customer', 'items.product'])
            ->where('rider_id', $riderId)
            ->latest()
            ->get();
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        return RiderLocation::create([
            'rider_id' => $request->rider_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id',
            'is_online' => 'required|boolean',
        ]);

        $rider = Rider::findOrFail($request->rider_id);

        $rider->update([
            'is_online' => $request->is_online
        ]);

        return $rider;
    }
    public function assignRider(Request $request, Order $order)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id'
        ]);

        $order->update([
            'rider_id' => $request->rider_id,
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);

        RiderAssigned::dispatch($order);

        return $order->load(['rider', 'customer', 'items']);
    }

    public function index()
    {
        return Rider::latest()->paginate();
    }

    public function store(RiderRequest $request)
    {
        return Rider::create(
            $request->validated()
        );
    }

    public function show(Rider $rider)
    {
        return $rider->load('latestLocation');
    }

    public function update(
        RiderRequest $request,
        Rider $rider
    ) {
        $rider->update(
            $request->validated()
        );

        return $rider;
    }

    public function destroy(Rider $rider)
    {
        $rider->delete();

        return response()->json([
            'message' => 'Rider deleted successfully'
        ]);
    }

    public function location(Rider $rider)
    {
        $location = $rider->latestLocation;

        if (!$location) {
            return response()->json([
                'message' => 'Location not found'
            ], 404);
        }

        return response()->json([
            'rider' => [
                'id' => $rider->id,
                'name' => $rider->name,
            ],
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'updated_at' => $location->created_at,
        ]);
    }

    public function trackOrder(Order $order)
    {
        if (!$order->rider_id) {
            return response()->json([
                'message' => 'Rider not assigned yet'
            ], 404);
        }

        $rider = $order->rider;

        $location = $rider->latestLocation;

        if (!$location) {
            return response()->json([
                'message' => 'Rider location unavailable'
            ], 404);
        }

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,

            'rider' => [
                'id' => $rider->id,
                'name' => $rider->name,
                'phone' => $rider->phone,
            ],

            'location' => [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'updated_at' => $location->created_at,
            ],

            'status' => $order->status,
        ]);
    }
}
