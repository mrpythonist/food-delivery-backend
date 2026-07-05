<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'variants'
        ]);

        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                filter_var(
                    $request->is_active,
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        if ($request->has('is_featured')) {
            $query->where(
                'is_featured',
                filter_var(
                    $request->is_featured,
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        return $query
            ->latest()
            ->paginate(
                $request->integer('per_page', 15)
            );
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $request->image,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return response()->json(
            $product->load([
                'category',
                'variants'
            ]),
            201
        );
    }

    public function show(Product $product)
    {
        return response()->json(
            $product->load([
                'category',
                'variants'
            ])
        );
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $request->image,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return response()->json(
            $product->load([
                'category',
                'variants'
            ])
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
