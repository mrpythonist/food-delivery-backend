<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Customer;
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
    public function show(int $customer)
    {
        Customer::findOrFail($customer);

        $cart = Cart::with([
            'items' => function ($query) {
                $query->orderBy('created_at');
            },
            'items.product',
            'items.variant',
        ])->firstOrCreate([
            'customer_id' => $customer,
        ]);

        return response()->json([
            'cart' => $cart,
            'subtotal' => $cart->items->sum('total_price'),
            'item_count' => $cart->items->sum('quantity'),
        ]);
    }

    public function add(Request $request, int $customer)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate([
            'customer_id' => $customer,
        ]);

        $variant = ProductVariant::findOrFail(
            $request->product_variant_id
        );

        $item = $cart->items()
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

        return $this->show($customer);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->quantity = $request->quantity;

        $cartItem->total_price =
            $cartItem->unit_price *
            $request->quantity;

        $cartItem->save();

        $cart = $cartItem->cart;

        return $this->show($cart->customer_id);
    }

    public function remove(CartItem $cartItem)
    {
        $cart = $cartItem->cart;

        $cartItem->delete();

        return $this->show($cart->customer_id);
    }

    public function clear(int $customer)
    {
        $cart = Cart::where(
            'customer_id',
            $customer
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
