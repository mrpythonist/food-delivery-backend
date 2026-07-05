<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\UpdateOrderPaymentRequest;
use Illuminate\Validation\ValidationException;
use App\Events\OrderPlaced;
use App\Events\OrderConfirmed;
use App\Events\OrderPrepared;
use App\Events\OrderDelivered;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $orders = Order::with([
            'customer',
            'rider',
            'items'
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $orders->where(function ($query) use ($search) {
                $query
                    ->where('order_number', 'ILIKE', "%{$search}%")
                    ->orWhereHas(
                        'customer',
                        fn($q) => $q
                            ->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('last_name', 'ILIKE', "%{$search}%")
                            ->orWhere('phone', 'ILIKE', "%{$search}%")
                    );
            });
        }

        if ($request->filled('status')) {
            $orders->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $orders->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $orders->whereDate('placed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $orders->whereDate('placed_at', '<=', $request->date_to);
        }

        $sortable = [
            'order_number',
            'placed_at',
            'total',
            'payment_status',
            'status'
        ];
        $sortBy = in_array($request->sort_by, $sortable)
            ? $request->sort_by
            : 'placed_at';
        $sortOrder = strtolower($request->sort_order) === 'asc'
            ? 'asc'
            : 'desc';

        return $orders
            ->orderBy($sortBy, $sortOrder)
            ->paginate($request->per_page ?? 10);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $total = 0;

            foreach ($request->items as $item) {

                $variant = ProductVariant::findOrFail(
                    $item['product_variant_id']
                );

                $total += (
                    $variant->price *
                    $item['quantity']
                );
            }

            $subtotal = $total;

            $settings = Setting::first();

            $deliveryFee =
                $subtotal >= $settings->free_delivery_above
                ? 0
                : $settings->delivery_fee;

            $discount = 0;
            $coupon = null;

            if ($request->filled('coupon_code')) {

                $coupon = Coupon::where(
                    'code',
                    strtoupper($request->coupon_code)
                )
                    ->where('is_active', true)
                    ->first();

                if (!$coupon) {
                    abort(
                        response()->json([
                            'message' => 'Invalid coupon'
                        ], 422)
                    );
                }

                if (
                    $coupon->expires_at &&
                    $coupon->expires_at->isPast()
                ) {
                    throw ValidationException::withMessages([
                        'coupon_code' => ['Invalid coupon.']
                    ]);
                }

                if (
                    $coupon->minimum_order &&
                    $subtotal < $coupon->minimum_order
                ) {
                    abort(
                        response()->json([
                            'message' => 'Minimum order amount not reached'
                        ], 422)
                    );
                }

                $discount =
                    $coupon->type === 'percentage'
                    ? ($subtotal * $coupon->value / 100)
                    : $coupon->value;
            }

            $totalAfterDiscount = max(
                0,
                ($subtotal - $discount) + $deliveryFee
            );

            $receiptPath = null;

            if ($request->hasFile('payment_receipt')) {

                $receiptPath = $request
                    ->file('payment_receipt')
                    ->store(
                        'payment-receipts',
                        'public'
                    );
            }

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'address_id' => $request->address_id,
                'order_number' => 'ORD-' . now()->format('YmdHis'),
                'status' => 'pending',

                'payment_method' => $request->payment_method,

                'payment_status' => in_array(
                    $request->payment_method,
                    ['easypaisa', 'jazzcash']
                )
                    ? 'awaiting_verification'
                    : 'pending',

                'transaction_id' => $request->transaction_id,

                'payment_receipt' => $receiptPath,

                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => round($discount, 2),
                'total' => round($totalAfterDiscount, 2),

                'notes' => $request->notes,
                'placed_at' => now(),
            ]);

            foreach ($request->items as $item) {

                $product = Product::findOrFail(
                    $item['product_id']
                );

                $variant = ProductVariant::findOrFail(
                    $item['product_variant_id']
                );

                OrderItem::create([

                    'order_id' => $order->id,

                    'product_id' => $product->id,

                    'product_variant_id' => $variant->id,

                    'product_name' => $product->name,

                    'variant_name' => $variant->name,

                    'quantity' => $item['quantity'],

                    'unit_price' => $variant->price,

                    'total_price' => (
                        $variant->price *
                        $item['quantity']
                    ),
                ]);
            }

            OrderPlaced::dispatch($order);

            return $order->load([
                'customer',
                'address',
                'coupon',
                'items.product',
                'items.variant',
            ]);
        });
    }

    /**
     * Display a specific order.
     */
    public function show(Order $order)
    {
        return $order->load([
            'customer',
            'address',
            'coupon',
            'rider',
            'items.product',
            'items.variant',
        ]);
    }

    /**
     * Update order status.
     */
    public function update(
        UpdateOrderRequest $request,
        Order $order
    ) {
        $order->update(
            $request->validated()
        );

        return $order->load([
            'customer',
            'address',
            'items.product',
            'items.variant'
        ]);
    }

    /**
     * Delete order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ) {
        $currentStatus = $order->status;

        $allowedTransitions = [

            'pending' => [
                'confirmed',
                'cancelled'
            ],

            'confirmed' => [
                'preparing',
                'cancelled'
            ],

            'preparing' => [
                'ready_for_pickup',
                'cancelled'
            ],

            'ready_for_pickup' => [
                'picked_up'
            ],

            'picked_up' => [
                'on_the_way'
            ],

            'on_the_way' => [
                'delivered'
            ],

            'delivered' => [],

            'cancelled' => []
        ];

        $newStatus = $request->status;

        if (
            ! in_array(
                $newStatus,
                $allowedTransitions[$currentStatus]
            )
        ) {
            return response()->json([
                'message' =>
                "Cannot change status from {$currentStatus} to {$newStatus}"
            ], 422);
        }

        $order->status = $newStatus;

        switch ($newStatus) {

            case 'confirmed':
                $order->confirmed_at = now();
                OrderConfirmed::dispatch($order);
                break;

            case 'preparing':
                $order->prepared_at = now();
                OrderPrepared::dispatch($order);
                break;

            case 'ready_for_pickup':
                $order->ready_for_pickup_at = now();
                break;

            case 'picked_up':
                $order->picked_up_at = now();
                break;

            case 'on_the_way':
                $order->on_the_way_at = now();
                break;

            case 'delivered':
                $order->delivered_at = now();
                OrderDelivered::dispatch($order);
                break;

            case 'cancelled':
                $order->cancelled_at = now();
                break;
        }

        $order->save();

        return $order->load([
            'customer',
            'address',
            'items.product',
            'items.variant'
        ]);
    }

    public function updatePaymentStatus(
        UpdateOrderPaymentRequest $request,
        Order $order
    ) {
        $currentStatus = $order->payment_status;

        $allowedTransitions = [

            'pending' => [
                'paid',
                'failed'
            ],

            'paid' => [
                'refunded'
            ],

            'failed' => [
                'pending'
            ],

            'refunded' => []

        ];

        $newStatus = $request->payment_status;

        if (
            ! in_array(
                $newStatus,
                $allowedTransitions[$currentStatus]
            )
        ) {
            return response()->json([
                'message' =>
                "Cannot change payment status from {$currentStatus} to {$newStatus}"
            ], 422);
        }

        $order->payment_status = $newStatus;

        $order->save();

        return $order->load([
            'customer',
            'address',
            'items.product',
            'items.variant'
        ]);
    }

    public function timeline(Order $order)
    {
        return response()->json([
            'order_number' => $order->order_number,

            'timeline' => [

                [
                    'status' => 'pending',
                    'timestamp' => $order->placed_at,
                ],

                [
                    'status' => 'confirmed',
                    'timestamp' => $order->confirmed_at,
                ],

                [
                    'status' => 'preparing',
                    'timestamp' => $order->prepared_at,
                ],

                [
                    'status' => 'ready_for_pickup',
                    'timestamp' => $order->ready_for_pickup_at,
                ],

                [
                    'status' => 'picked_up',
                    'timestamp' => $order->picked_up_at,
                ],

                [
                    'status' => 'on_the_way',
                    'timestamp' => $order->on_the_way_at,
                ],

                [
                    'status' => 'delivered',
                    'timestamp' => $order->delivered_at,
                ],

                [
                    'status' => 'cancelled',
                    'timestamp' => $order->cancelled_at,
                ],
            ],
        ]);
    }
}
