<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Rider;
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
        $cards = [
            'today_orders' => Order::whereDate('placed_at', today())->count(),

            'today_revenue' => Order::whereDate('placed_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),

            'pending_orders' => Order::where('status', 'pending')->count(),

            'online_riders' => Rider::where('is_online', true)->count(),
        ];

        $monthlySales = Order::selectRaw("
            EXTRACT(MONTH FROM placed_at) as month,
            COALESCE(SUM(total),0) as sales
        ")
            ->whereYear('placed_at', now()->year)
            ->where('payment_status', 'paid')
            ->groupByRaw('EXTRACT(MONTH FROM placed_at)')
            ->pluck('sales', 'month');

        $salesByMonth = [];

        for ($month = 1; $month <= 12; $month++) {

            $salesByMonth[] = [

                'month' => date(
                    'M',
                    mktime(0, 0, 0, $month, 1)
                ),

                'sales' => (float) ($monthlySales[$month] ?? 0)
            ];
        }

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $weeklyIncome = Order::selectRaw("
            EXTRACT(DOW FROM placed_at) as day,
            COALESCE(SUM(total),0) as income
        ")
            ->whereBetween('placed_at', [$weekStart, $weekEnd])
            ->where('payment_status', 'paid')
            ->groupByRaw('EXTRACT(DOW FROM placed_at)')
            ->pluck('income', 'day');

        $days = [
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            0 => 'Sun'
        ];

        $incomeOverview = [];

        foreach ($days as $index => $name) {

            $incomeOverview[] = [

                'day' => $name,

                'income' => (float) ($weeklyIncome[$index] ?? 0)
            ];
        }

        $latestOrders = Order::with([
            'customer',
            'items'
        ])
            ->latest('placed_at')
            ->take(10)
            ->get()
            ->map(function ($order) {

                return [

                    'order_number' => $order->order_number,

                    'placed_at' => $order->placed_at,

                    'customer' => $order->customer?->first_name . " " . $order->customer?->last_name,

                    'items' => $order->items->sum('quantity'),

                    'status' => $order->status,

                    'total' => (float) $order->total,
                ];
            });

        return [

            'cards' => $cards,

            'sales_by_month' => $salesByMonth,

            'income_overview' => $incomeOverview,

            'latest_orders' => $latestOrders,
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
