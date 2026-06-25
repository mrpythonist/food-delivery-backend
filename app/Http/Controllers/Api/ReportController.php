<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function stats()
    {
        $orders = Order::query();

        $this->applyDateFilter(
            $orders,
            'placed_at'
        );

        return response()->json([

            'total_orders' => (clone $orders)
                ->count(),

            'pending_orders' => (clone $orders)
                ->where(
                    'status',
                    'pending'
                )
                ->count(),

            'confirmed_orders' => (clone $orders)
                ->where(
                    'status',
                    'confirmed'
                )
                ->count(),

            'delivered_orders' => (clone $orders)
                ->where(
                    'status',
                    'delivered'
                )
                ->count(),

            'cancelled_orders' => (clone $orders)
                ->where(
                    'status',
                    'cancelled'
                )
                ->count(),

            'customers' => Customer::count(),

            'revenue' => (clone $orders)
                ->where(
                    'status',
                    'delivered'
                )
                ->sum('total'),
        ]);
    }

    public function dashboard()
    {
        $orders = Order::query();

        $this->applyDateFilter(
            $orders,
            'placed_at'
        );

        return [

            'total_customers' => Customer::count(),

            'total_products' => Product::count(),

            'total_orders' => (clone $orders)->count(),

            'pending_orders' => (clone $orders)
                ->where('status', 'pending')
                ->count(),

            'completed_orders' => (clone $orders)
                ->where('status', 'delivered')
                ->count(),

            'total_revenue' => (clone $orders)
                ->where('status', 'delivered')
                ->sum('total'),

            'today_orders' => Order::whereDate(
                'placed_at',
                today()
            )->count(),

            'today_revenue' => Order::where(
                'status',
                'delivered'
            )
                ->whereDate(
                    'placed_at',
                    today()
                )
                ->sum('total'),
        ];
    }

    public function topProducts()
    {
        $query = OrderItem::query()
            ->join(
                'orders',
                'orders.id',
                '=',
                'order_items.order_id'
            );

        $this->applyDateFilter(
            $query,
            'orders.placed_at'
        );

        return $query
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as qty_sold'),
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name'
            )
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();
    }

    public function salesSummary()
    {
        $query = Order::where(
            'status',
            'delivered'
        );

        $this->applyDateFilter(
            $query,
            'placed_at'
        );

        return [
            'total_sales' => $query->sum('total'),
            'total_orders' => $query->count(),
            'average_order' => round(
                $query->avg('total') ?? 0,
                2
            ),
        ];
    }

    public function ordersByStatus()
    {
        $query = Order::query();

        $this->applyDateFilter(
            $query,
            'placed_at'
        );

        return $query
            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->orderBy('status')
            ->get();
    }

    private function applyDateFilter($query, $column = 'created_at')
    {
        if (request()->filled('from')) {
            $query->whereDate(
                $column,
                '>=',
                request('from')
            );
        }

        if (request()->filled('to')) {
            $query->whereDate(
                $column,
                '<=',
                request('to')
            );
        }

        return $query;
    }
}
