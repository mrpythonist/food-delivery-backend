<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Customer;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\ApplyCouponRequest;

class CouponController extends Controller
{
    public function index()
    {
        return Coupon::latest()->get();
    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();

        $data['code'] = strtoupper($data['code']);

        return Coupon::create($data);
    }

    public function show(Coupon $coupon)
    {
        return $coupon;
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();

        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $coupon->update($data);

        return response()->json($coupon);
    }
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json([
            'message' => 'Coupon deleted'
        ]);
    }

    public function apply(ApplyCouponRequest $request, int $customer)
    {
        Customer::findOrFail($customer);

        $coupon = Coupon::where(
            'code',
            strtoupper($request->code)
        )
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'message' => 'Invalid coupon',
            ], 422);
        }

        if (
            $coupon->expires_at &&
            $coupon->expires_at->isPast()
        ) {
            return response()->json([
                'message' => 'Coupon expired',
            ], 422);
        }

        $cart = Cart::with('items')
            ->where('customer_id', $customer)
            ->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart not found',
            ], 404);
        }

        $subtotal = $cart->items->sum('total_price');

        if (
            $coupon->minimum_order &&
            $subtotal < $coupon->minimum_order
        ) {
            return response()->json([
                'message' => 'Minimum order not reached',
            ], 422);
        }

        $discount = $coupon->type === 'percentage'
            ? ($subtotal * $coupon->value / 100)
            : $coupon->value;

        $discount = min($discount, $subtotal);

        return response()->json([
            'coupon' => $coupon,
            'subtotal' => $subtotal,
            'discount' => round($discount, 2),
            'total' => round($subtotal - $discount, 2),
        ]);
    }
}
