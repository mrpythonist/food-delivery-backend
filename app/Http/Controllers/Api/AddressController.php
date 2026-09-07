<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Address;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function index(Customer $customer)
    {
        return $customer->addresses()->latest()->paginate(20);
    }

    public function store(AddressRequest $request, Customer $customer)
    {
        $address = $customer->addresses()->create(
            $request->validated()
        );

        return response()->json($address, 201);
    }

    public function show(Customer $customer, Address $address)
    {

        return $address;
    }

    public function update(
        AddressRequest $request,
        Customer $customer,
        Address $address
    ) {

        $address->update(
            $request->validated()
        );

        return $address->fresh();
    }

    public function destroy(
        Customer $customer,
        Address $address
    ) {

        $address->delete();

        return response()->json([
            'message' => 'Address deleted'
        ]);
    }
}
