<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
            'message' => 'Categories retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => new CategoryResource($category->loadCount('products')),
            'message' => 'Category retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }
}
