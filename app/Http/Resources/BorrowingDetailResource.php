<?php

namespace App\Http\Resources;

use App\Models\BorrowingDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BorrowingDetail
 */
class BorrowingDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'condition_on_return' => $this->condition_on_return,
        ];
    }
}
