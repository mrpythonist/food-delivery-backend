<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $variants = ProductVariant::with('product')->get();

        if (
            $customers->isEmpty() ||
            $variants->isEmpty()
        ) {
            return;
        }

        $statuses = [
            'pending',
            'confirmed',
            'preparing',
            'on_the_way',
            'delivered',
            'cancelled',
        ];

        for ($i = 1; $i <= 20; $i++) {

            $customer = $customers->random();

            $address = Address::where(
                'customer_id',
                $customer->id
            )->first();

            if (!$address) {
                continue;
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'address_id' => $address->id,
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'status' => $statuses[array_rand($statuses)],
                'subtotal' => 0,
                'delivery_fee' => 100,
                'discount' => 0,
                'total' => 0,
                'notes' => fake()->optional()->sentence(),
                'placed_at' => now()->subDays(rand(0, 30)),
            ]);

            $subtotal = 0;

            $selectedVariants = $variants->random(
                rand(1, 4)
            );

            foreach ($selectedVariants as $variant) {

                $quantity = rand(1, 3);

                $unitPrice = $variant->price;

                $totalPrice = $unitPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                $subtotal += $totalPrice;
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + 100,
            ]);
        }
    }
}
