<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *   path="/api/v1/categories",
     *   tags={"Categories"},
     *   summary="List categories with product counts",
     *   security={{"sanctum":{}}},
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope"))
     * )
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::withCount('products')->orderBy('name')->get();

        return $this->success(
            CategoryResource::collection($categories),
            'Categories retrieved successfully.',
        );
    }

    /**
     * @OA\Get(
     *   path="/api/v1/categories/{category}",
     *   tags={"Categories"},
     *   summary="Get a single category",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function show(Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        return $this->success(
            new CategoryResource($category->loadCount('products')),
            'Category retrieved successfully.',
        );
    }
}
