<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category')
            ->search($request->input('search'))
            ->when($request->input('category'), fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when($request->input('condition'), fn ($q, $condition) => $q->where('condition', $condition))
            ->latest()
            ->paginate($request->integer('per_page') ?: 15);

        return response()->json([
            'data' => ProductResource::collection($products)->response()->getData(true),
            'message' => 'Products retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
        ]);

        $product = Product::create($validated);

        return response()->json([
            'data' => new ProductResource($product->load('category')),
            'message' => 'Product created successfully.',
            'status' => Response::HTTP_CREATED,
        ], Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => new ProductResource($product->load('category')),
            'message' => 'Product retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'condition' => ['sometimes', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
        ]);

        $product->update($validated);

        return response()->json([
            'data' => new ProductResource($product->load('category')),
            'message' => 'Product updated successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $activeBorrowings = $product->borrowingDetails()
            ->whereHas('borrowing', fn ($query) => $query->where('status', 'dipinjam'))
            ->exists();

        if ($activeBorrowings) {
            return response()->json([
                'data' => null,
                'message' => 'Product cannot be deleted because it has active borrowings.',
                'status' => Response::HTTP_CONFLICT,
            ], Response::HTTP_CONFLICT);
        }

        $product->delete();

        return response()->json([
            'data' => null,
            'message' => 'Product deleted successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }
}
