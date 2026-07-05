<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\ApiResponse;
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
    use ApiResponse;

    /**
     * @OA\Get(
     *   path="/api/v1/borrowings",
     *   tags={"Borrowings"},
     *   summary="List borrowings",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"dipinjam","dikembalikan","terlambat"})),
     *   @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date")),
     *   @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date")),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Borrowing::class);

        $borrowings = Borrowing::with(['user', 'borrowingDetails.product'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('date_from'), fn ($q, $date) => $q->where('borrowed_at', '>=', $date))
            ->when($request->input('date_to'), fn ($q, $date) => $q->where('borrowed_at', '<=', $date))
            ->latest()
            ->paginate($request->integer('per_page') ?: 15);

        return $this->success(
            BorrowingResource::collection($borrowings)->response()->getData(true),
            'Borrowings retrieved successfully.',
        );
    }

    /**
     * @OA\Post(
     *   path="/api/v1/borrowings",
     *   tags={"Borrowings"},
     *   summary="Create a borrowing (transactional; stock is decremented atomically)",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"borrower_name","borrowed_at","due_at","items"},
     *       @OA\Property(property="borrower_name", type="string", example="Budi"),
     *       @OA\Property(property="borrowed_at", type="string", format="date", example="2026-07-05"),
     *       @OA\Property(property="due_at", type="string", format="date", example="2026-07-12"),
     *       @OA\Property(property="notes", type="string", nullable=true),
     *       @OA\Property(
     *         property="items",
     *         type="array",
     *         @OA\Items(
     *           required={"product_id","quantity"},
     *           @OA\Property(property="product_id", type="integer", example=1),
     *           @OA\Property(property="quantity", type="integer", minimum=1, example=2)
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=422, description="Insufficient stock or validation error", @OA\JsonContent(ref="#/components/schemas/ApiError")),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Borrowing::class);

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
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->success(
            new BorrowingResource($borrowing->load(['user', 'borrowingDetails.product'])),
            'Borrowing created successfully.',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @OA\Get(
     *   path="/api/v1/borrowings/{borrowing}",
     *   tags={"Borrowings"},
     *   summary="Get a single borrowing",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="borrowing", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function show(Borrowing $borrowing): JsonResponse
    {
        $this->authorize('view', $borrowing);

        return $this->success(
            new BorrowingResource($borrowing->load(['user', 'approver', 'borrowingDetails.product'])),
            'Borrowing retrieved successfully.',
        );
    }

    /**
     * @OA\Put(
     *   path="/api/v1/borrowings/{borrowing}",
     *   tags={"Borrowings"},
     *   summary="Update notes or due date",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="borrowing", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="notes", type="string", nullable=true),
     *       @OA\Property(property="due_at", type="string", format="date")
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope"))
     * )
     */
    public function update(Request $request, Borrowing $borrowing): JsonResponse
    {
        $this->authorize('update', $borrowing);

        $validated = $request->validate([
            'notes' => ['sometimes', 'nullable', 'string'],
            'due_at' => ['sometimes', 'date'],
        ]);

        $borrowing->update($validated);

        return $this->success(
            new BorrowingResource($borrowing->load(['user', 'borrowingDetails.product'])),
            'Borrowing updated successfully.',
        );
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/borrowings/{borrowing}",
     *   tags={"Borrowings"},
     *   summary="Delete a returned borrowing",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="borrowing", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=409, description="Borrowing still active", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function destroy(Borrowing $borrowing): JsonResponse
    {
        $this->authorize('delete', $borrowing);

        if ($borrowing->status === 'dipinjam') {
            return $this->error(
                'Cannot delete an active borrowing. Return the items first.',
                Response::HTTP_CONFLICT,
            );
        }

        $borrowing->delete();

        return $this->success(null, 'Borrowing deleted successfully.');
    }

    /**
     * @OA\Patch(
     *   path="/api/v1/borrowings/{borrowing}/return",
     *   tags={"Borrowings"},
     *   summary="Mark items as returned and restock (transactional)",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="borrowing", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Returned", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope")),
     *   @OA\Response(response=409, description="Already returned", @OA\JsonContent(ref="#/components/schemas/ApiError"))
     * )
     */
    public function returnItems(Borrowing $borrowing): JsonResponse
    {
        $this->authorize('return', $borrowing);

        if ($borrowing->status !== 'dipinjam') {
            return $this->error(
                'This borrowing has already been returned.',
                Response::HTTP_CONFLICT,
            );
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

        return $this->success(
            new BorrowingResource($borrowing->fresh(['user', 'borrowingDetails.product'])),
            'Items returned successfully. Stock has been updated.',
        );
    }
}
