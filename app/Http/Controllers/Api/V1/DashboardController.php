<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *   path="/api/v1/dashboard/stats",
     *   tags={"Dashboard"},
     *   summary="Aggregate KPIs and 12-month borrowing trend",
     *   security={{"sanctum":{}}},
     *
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *
     *     @OA\JsonContent(
     *       allOf={
     *
     *         @OA\Schema(ref="#/components/schemas/ApiEnvelope"),
     *         @OA\Schema(
     *
     *           @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="total_stock", type="integer"),
     *             @OA\Property(property="borrowed_count", type="integer"),
     *             @OA\Property(property="available_stock", type="integer"),
     *             @OA\Property(property="total_categories", type="integer"),
     *             @OA\Property(property="total_products", type="integer"),
     *             @OA\Property(property="low_stock_products", type="integer"),
     *             @OA\Property(property="active_borrowings", type="integer"),
     *             @OA\Property(property="overdue_borrowings", type="integer"),
     *             @OA\Property(property="monthly_trends", type="array", @OA\Items(type="object",
     *               @OA\Property(property="month", type="string", example="2026-07"),
     *               @OA\Property(property="total", type="integer")
     *             ))
     *           )
     *         )
     *       }
     *     )
     *   )
     * )
     */
    public function stats(): JsonResponse
    {
        $totalStock = (int) Product::sum('stock');
        $borrowedCount = (int) BorrowingDetail::whereHas(
            'borrowing',
            fn ($q) => $q->where('status', 'dipinjam'),
        )->sum('quantity');
        $availableProductTypes = Product::where('stock', '>', 0)->count();

        $monthly = Borrowing::select(
            DB::raw("to_char(borrowed_at, 'YYYY-MM') as month"),
            DB::raw('count(*) as total'),
        )
            ->where('borrowed_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $trends[] = [
                'month' => $key,
                'total' => (int) ($monthly[$key] ?? 0),
            ];
        }

        return $this->success([
            'total_stock' => $totalStock,
            'borrowed_count' => $borrowedCount,
            'available_product_types' => $availableProductTypes,
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock', '<=', 5)->count(),
            'active_borrowings' => Borrowing::where('status', 'dipinjam')->count(),
            'overdue_borrowings' => Borrowing::where('status', 'dipinjam')
                ->where('due_at', '<', Carbon::today())
                ->count(),
            'monthly_trends' => $trends,
        ], 'Dashboard stats retrieved successfully.');
    }
}
