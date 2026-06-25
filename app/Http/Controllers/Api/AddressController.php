<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function index()
    {
        return Address::with('customer')
            ->latest()
            ->paginate(20);
    }

    public function store(AddressRequest $request)
    {
        $address = Address::create(
            $request->validated()
        );

        return response()->json(
            $address,
            201
        );
    }

    public function show(Address $address)
    {
        return $address->load('customer');
    }

    public function update(
        AddressRequest $request,
        Address $address
    ) {
        $address->update(
            $request->validated()
        );

        return $address->fresh();
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->json([
            'message' => 'Address deleted'
        ]);
    }
}