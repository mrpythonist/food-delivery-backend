<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with(['customer', 'items.product', 'items.variant'])
            ->whereHas('items')
            ->where('updated_at', '<=', now()->subHours(24))
            ->latest('updated_at')
            ->paginate($request->integer('per_page', 15));

        return $carts;
    }
    public function show(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $cart = Cart::with([
            'items.product',
            'items.variant'
        ])
            ->firstOrCreate([
                'customer_id' => $request->customer_id
            ]);

        return response()->json([
            'cart' => $cart,
            'subtotal' => $cart->items->sum('total_price'),
            'item_count' => $cart->items->sum('quantity'),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate([
            'customer_id' => $request->customer_id
        ]);

        $variant = ProductVariant::findOrFail(
            $request->product_variant_id
        );

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($item) {

            $item->quantity += $request->quantity;

            $item->total_price =
                $item->quantity *
                $item->unit_price;

            $item->save();
        } else {

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'unit_price' => $variant->price,
                'total_price' => $variant->price * $request->quantity,
            ]);
        }

        return $this->show(new Request([
            'customer_id' => $request->customer_id
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail(
            $request->cart_item_id
        );

        $item->quantity = $request->quantity;

        $item->total_price =
            $item->unit_price *
            $request->quantity;

        $item->save();

        $cart = Cart::findOrFail(
            $item->cart_id
        );

        return $this->show(new Request([
            'customer_id' => $cart->customer_id
        ]));
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        $item = CartItem::findOrFail(
            $request->cart_item_id
        );

        $cart = Cart::findOrFail(
            $item->cart_id
        );

        $item->delete();

        return $this->show(new Request([
            'customer_id' => $cart->customer_id
        ]));
    }

    public function clear(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $cart = Cart::where(
            'customer_id',
            $request->customer_id
        )->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'message' => 'Cart cleared',
            'cart' => [],
            'subtotal' => 0,
            'item_count' => 0,
        ]);
    }
}
