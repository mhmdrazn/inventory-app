<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *   path="/api/v1/products",
     *   tags={"Products"},
     *   summary="List products",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="category", in="query", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="condition", in="query", @OA\Schema(type="string", enum={"baik","rusak_ringan","rusak_berat"})),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/ApiError")),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::with('category')
            ->search($request->input('search'))
            ->when($request->input('category'), fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when($request->input('condition'), fn ($q, $condition) => $q->where('condition', $condition))
            ->latest()
            ->paginate($request->integer('per_page') ?: 15);

        return $this->success(
            ProductResource::collection($products)->response()->getData(true),
            'Products retrieved successfully.',
        );
    }

    /**
     * @OA\Post(
     *   path="/api/v1/products",
     *   tags={"Products"},
     *   summary="Create a product",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"code","name","category_id","stock","condition"},
     *       @OA\Property(property="code", type="string", example="INV-ELE-007"),
     *       @OA\Property(property="name", type="string", example="Monitor LG 24 inch"),
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="stock", type="integer", minimum=0, example=10),
     *       @OA\Property(property="location", type="string", nullable=true, example="Gudang IT Lt. 2"),
     *       @OA\Property(property="condition", type="string", enum={"baik","rusak_ringan","rusak_berat"})
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ApiError")),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
        ]);

        $product = Product::create($validated);

        return $this->success(
            new ProductResource($product->load('category')),
            'Product created successfully.',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @OA\Get(
     *   path="/api/v1/products/{product}",
     *   tags={"Products"},
     *   summary="Get a single product",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return $this->success(
            new ProductResource($product->load('category')),
            'Product retrieved successfully.',
        );
    }

    /**
     * @OA\Put(
     *   path="/api/v1/products/{product}",
     *   tags={"Products"},
     *   summary="Update a product (partial)",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="code", type="string"),
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="category_id", type="integer"),
     *       @OA\Property(property="stock", type="integer", minimum=0),
     *       @OA\Property(property="location", type="string", nullable=true),
     *       @OA\Property(property="condition", type="string", enum={"baik","rusak_ringan","rusak_berat"})
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ApiError")),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'condition' => ['sometimes', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
        ]);

        $product->update($validated);

        return $this->success(
            new ProductResource($product->load('category')),
            'Product updated successfully.',
        );
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/products/{product}",
     *   tags={"Products"},
     *   summary="Delete a product",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=409, description="Product has active borrowings", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $activeBorrowings = $product->borrowingDetails()
            ->whereHas('borrowing', fn ($query) => $query->where('status', 'dipinjam'))
            ->exists();

        if ($activeBorrowings) {
            return $this->error(
                'Product cannot be deleted because it has active borrowings.',
                Response::HTTP_CONFLICT,
            );
        }

        $product->delete();

        return $this->success(null, 'Product deleted successfully.');
    }
}
