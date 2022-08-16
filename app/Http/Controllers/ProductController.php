<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::limit($request->query('limit') ?? null)
                            ->sortProduct($request->query('sortby') ?? null)
                            ->orderProduct($request->query('orderby') ?? null)
                            ->get();
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Product inserted successfully',
            'product' => new ProductResource($product)
        ], 201);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Product $product, StoreProductRequest $request)
    {
        $product->update($request->validated());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => new ProductResource($product->fresh())
        ], 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
