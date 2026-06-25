<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'first_name',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        return $query
            ->latest()
            ->paginate(
                $request->integer('per_page', 15)
            );
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create(
            $request->validated()
        );

        return response()->json(
            $customer,
            201
        );
    }

    public function show(Customer $customer)
    {
        return $customer->load([
            'addresses',
            'orders'
        ]);
    }

    public function update(
        CustomerRequest $request,
        Customer $customer
    ) {
        $customer->update(
            $request->validated()
        );

        return $customer->fresh();
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted'
        ]);
    }

    public function orders(Customer $customer)
    {
        return $customer->orders()
            ->with([
                'address',
                'items.product',
                'items.variant',
            ])
            ->select([
                'id',
                'customer_id',
                'order_number',
                'status',
                'total',
                'placed_at',
                'created_at',
            ])
            ->latest()
            ->get();
    }
}
