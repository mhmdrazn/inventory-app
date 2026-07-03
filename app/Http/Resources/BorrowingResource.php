<?php

namespace App\Http\Resources;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Borrowing
 */
class BorrowingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'borrower_name' => $this->borrower_name,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'approver' => $this->whenLoaded('approver', fn () => $this->approver ? [
                'id' => $this->approver->id,
                'name' => $this->approver->name,
            ] : null),
            'status' => $this->status,
            'borrowed_at' => $this->borrowed_at?->toDateString(),
            'due_at' => $this->due_at?->toDateString(),
            'returned_at' => $this->returned_at?->toDateString(),
            'is_overdue' => $this->isOverdue(),
            'notes' => $this->notes,
            'details' => BorrowingDetailResource::collection($this->whenLoaded('borrowingDetails')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
