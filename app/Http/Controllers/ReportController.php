<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Display the reports page with filter form and summary tables.
     */
    public function index(Request $request): View
    {
        [$dateFrom, $dateTo, $status] = $this->parseFilters($request);

        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        $borrowings = $this->filteredBorrowings($dateFrom, $dateTo, $status)
            ->with(['user', 'borrowingDetails.product'])
            ->latest('borrowed_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('reports.index', [
            'products' => $products,
            'borrowings' => $borrowings,
            'categories' => $categories,
            'filters' => [
                'date_from' => $dateFrom?->toDateString(),
                'date_to' => $dateTo?->toDateString(),
                'status' => $status,
            ],
        ]);
    }

    /**
     * Generate a PDF report of the inventory and borrowing history.
     */
    public function exportPdf(Request $request): Response
    {
        [$dateFrom, $dateTo, $status] = $this->parseFilters($request);

        $products = Product::with('category')->orderBy('name')->get();

        $borrowings = $this->filteredBorrowings($dateFrom, $dateTo, $status)
            ->with(['user', 'borrowingDetails.product'])
            ->latest('borrowed_at')
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'products' => $products,
            'borrowings' => $borrowings,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
            ],
            'generatedAt' => Carbon::now(),
            'generatedBy' => $request->user()->name,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-inventaris-'.Carbon::now()->format('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate an Excel workbook with Inventaris and Peminjaman sheets.
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$dateFrom, $dateTo, $status] = $this->parseFilters($request);

        $filename = 'laporan-inventaris-'.Carbon::now()->format('Ymd-His').'.xlsx';

        return Excel::download(
            new InventoryExport($dateFrom, $dateTo, $status),
            $filename,
        );
    }

    /**
     * Parse date-range and status filters from the request.
     *
     * @return array{0: ?Carbon, 1: ?Carbon, 2: ?string}
     */
    private function parseFilters(Request $request): array
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'))->startOfDay()
            : null;

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'))->endOfDay()
            : null;

        $status = $request->input('status');

        if (! in_array($status, ['dipinjam', 'dikembalikan', 'terlambat'], true)) {
            $status = null;
        }

        return [$dateFrom, $dateTo, $status];
    }

    /**
     * Build a filtered Borrowing query.
     */
    private function filteredBorrowings(?Carbon $dateFrom, ?Carbon $dateTo, ?string $status)
    {
        return Borrowing::query()
            ->when($dateFrom, fn ($q) => $q->where('borrowed_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('borrowed_at', '<=', $dateTo))
            ->when($status, fn ($q) => $q->where('status', $status));
    }
}
