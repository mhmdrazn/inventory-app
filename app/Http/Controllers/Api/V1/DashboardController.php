<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalStock = (int) Product::sum('stock');
        $borrowedCount = (int) BorrowingDetail::whereHas(
            'borrowing',
            fn ($q) => $q->where('status', 'dipinjam'),
        )->sum('quantity');
        $availableStock = $totalStock - $borrowedCount;

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

        return response()->json([
            'data' => [
                'total_stock' => $totalStock,
                'borrowed_count' => $borrowedCount,
                'available_stock' => $availableStock,
                'total_categories' => Category::count(),
                'total_products' => Product::count(),
                'low_stock_products' => Product::where('stock', '<=', 5)->count(),
                'active_borrowings' => Borrowing::where('status', 'dipinjam')->count(),
                'overdue_borrowings' => Borrowing::where('status', 'dipinjam')
                    ->where('due_at', '<', Carbon::today())
                    ->count(),
                'monthly_trends' => $trends,
            ],
            'message' => 'Dashboard stats retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }
}
