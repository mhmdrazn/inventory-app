<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    public function index(Request $request): View
    {
        $borrowings = Borrowing::with(['user', 'borrowingDetails'])
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('search'), fn ($query, $search) => $query->where('borrower_name', 'ilike', "%{$search}%"))
            ->when($request->input('date_from'), fn ($query, $date) => $query->where('borrowed_at', '>=', $date))
            ->when($request->input('date_to'), fn ($query, $date) => $query->where('borrowed_at', '<=', $date))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function create(): View
    {
        $products = Product::available()->orderBy('name')->get();

        return view('borrowings.create', compact('products'));
    }

    public function store(StoreBorrowingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request): void {
            $borrowing = Borrowing::create([
                'user_id' => $request->user()->id,
                'borrower_name' => $validated['borrower_name'],
                'status' => 'dipinjam',
                'borrowed_at' => $validated['borrowed_at'],
                'due_at' => $validated['due_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}, diminta: {$item['quantity']}");
                }

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }
        });

        return redirect()->route('borrowings.index')
            ->with('success', 'Peminjaman berhasil dibuat.');
    }

    public function show(Borrowing $borrowing): View
    {
        $borrowing->load(['user', 'approver', 'borrowingDetails.product']);

        return view('borrowings.show', compact('borrowing'));
    }

    public function edit(Borrowing $borrowing): View
    {
        return view('borrowings.edit', compact('borrowing'));
    }

    public function update(Request $request, Borrowing $borrowing): RedirectResponse
    {
        abort(501, 'Belum diimplementasikan.');
    }

    public function destroy(Borrowing $borrowing): RedirectResponse
    {
        abort(501, 'Belum diimplementasikan.');
    }

    public function returnItems(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'dipinjam') {
            return redirect()->route('borrowings.show', $borrowing)
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        DB::transaction(function () use ($borrowing): void {
            $borrowing->update([
                'status' => 'dikembalikan',
                'returned_at' => Carbon::today(),
            ]);

            foreach ($borrowing->borrowingDetails as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }
        });

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Barang berhasil dikembalikan. Stok telah diperbarui.');
    }
}
