<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with summary stats, charts, and tables.
     */
    public function index(): View
    {
        $totalStock = Product::sum('stock');
        $borrowedCount = BorrowingDetail::whereHas('borrowing', fn ($q) => $q->where('status', 'dipinjam'))->sum('quantity');
        $availableProductTypes = Product::where('stock', '>', 0)->count();
        $totalCategories = Category::count();

        // Peminjaman per bulan (12 bulan terakhir)
        $monthlyBorrowings = Borrowing::select(
            DB::raw("to_char(borrowed_at, 'YYYY-MM') as month"),
            DB::raw('count(*) as total'),
        )
            ->where('borrowed_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $chartLabels[] = $date->translatedFormat('M Y');
            $found = $monthlyBorrowings->firstWhere('month', $monthKey);
            $chartData[] = $found ? $found->total : 0;
        }

        // Peminjaman terbaru (5 terakhir) — view only reads borrower_name, borrowed_at, status.
        $recentBorrowings = Borrowing::select(['id', 'borrower_name', 'borrowed_at', 'status'])
            ->latest()
            ->take(5)
            ->get();

        // Barang stok menipis (<= 5)
        $lowStockProducts = Product::with('category:id,name')
            ->select(['id', 'name', 'stock', 'category_id'])
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        // Peminjaman overdue — view reads borrower_name, due_at only.
        $overdueBorrowings = Borrowing::select(['id', 'borrower_name', 'due_at'])
            ->where('status', 'dipinjam')
            ->where('due_at', '<', Carbon::today())
            ->get();

        return view('dashboard', compact(
            'totalStock',
            'borrowedCount',
            'availableProductTypes',
            'totalCategories',
            'chartLabels',
            'chartData',
            'recentBorrowings',
            'lowStockProducts',
            'overdueBorrowings',
        ));
    }
}
