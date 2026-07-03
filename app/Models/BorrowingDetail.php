<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingDetail extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'borrowing_id',
        'product_id',
        'quantity',
        'condition_on_return',
    ];

    /**
     * @return BelongsTo<Borrowing, $this>
     */
    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
