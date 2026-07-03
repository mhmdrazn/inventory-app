<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowingResource;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $borrowings = Borrowing::with(['user', 'borrowingDetails.product'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('date_from'), fn ($q, $date) => $q->where('borrowed_at', '>=', $date))
            ->when($request->input('date_to'), fn ($q, $date) => $q->where('borrowed_at', '<=', $date))
            ->latest()
            ->paginate($request->integer('per_page') ?: 15);

        return response()->json([
            'data' => BorrowingResource::collection($borrowings)->response()->getData(true),
            'message' => 'Borrowings retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'borrower_name' => ['required', 'string', 'max:255'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after:borrowed_at'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $borrowing = DB::transaction(function () use ($validated, $request) {
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

                return $borrowing;
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'data' => new BorrowingResource($borrowing->load(['user', 'borrowingDetails.product'])),
            'message' => 'Borrowing created successfully.',
            'status' => Response::HTTP_CREATED,
        ], Response::HTTP_CREATED);
    }

    public function show(Borrowing $borrowing): JsonResponse
    {
        return response()->json([
            'data' => new BorrowingResource($borrowing->load(['user', 'approver', 'borrowingDetails.product'])),
            'message' => 'Borrowing retrieved successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function update(Request $request, Borrowing $borrowing): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['sometimes', 'nullable', 'string'],
            'due_at' => ['sometimes', 'date'],
        ]);

        $borrowing->update($validated);

        return response()->json([
            'data' => new BorrowingResource($borrowing->load(['user', 'borrowingDetails.product'])),
            'message' => 'Borrowing updated successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function destroy(Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status === 'dipinjam') {
            return response()->json([
                'data' => null,
                'message' => 'Cannot delete an active borrowing. Return the items first.',
                'status' => Response::HTTP_CONFLICT,
            ], Response::HTTP_CONFLICT);
        }

        $borrowing->delete();

        return response()->json([
            'data' => null,
            'message' => 'Borrowing deleted successfully.',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function returnItems(Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status !== 'dipinjam') {
            return response()->json([
                'data' => null,
                'message' => 'This borrowing has already been returned.',
                'status' => Response::HTTP_CONFLICT,
            ], Response::HTTP_CONFLICT);
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

        return response()->json([
            'data' => new BorrowingResource($borrowing->fresh(['user', 'borrowingDetails.product'])),
            'message' => 'Items returned successfully. Stock has been updated.',
            'status' => Response::HTTP_OK,
        ]);
    }
}
