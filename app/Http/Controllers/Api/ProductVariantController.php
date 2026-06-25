<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    public function index()
    {
        return ProductVariant::with('product')->get();
    }

    public function store(ProductVariantRequest $request)
    {
        if ($request->is_default) {
            ProductVariant::where(
                'product_id',
                $request->product_id
            )->update([
                'is_default' => false
            ]);
        }

        $variant = ProductVariant::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'price' => $request->price,
            'is_default' => $request->is_default ?? false,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json(
            $variant->load('product'),
            201
        );
    }

    public function show(ProductVariant $variant)
    {
        return $variant->load('product');
    }

    public function update(
        ProductVariantRequest $request,
        ProductVariant $variant
    ) {
        if ($request->is_default) {
            ProductVariant::where(
                'product_id',
                $request->product_id
            )->update([
                'is_default' => false
            ]);
        }

        $variant->update([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'price' => $request->price,
            'is_default' => $request->is_default ?? false,
            'is_active' => $request->is_active ?? true,
        ]);

        return $variant->load('product');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return response()->json([
            'message' => 'Variant deleted successfully'
        ]);
    }
}