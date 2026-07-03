<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'stock',
        'location',
        'condition',
        'image',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => 'string',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<BorrowingDetail, $this>
     */
    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    /**
     * Scope to search products by name or code.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search): void {
            $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('code', 'ilike', "%{$search}%");
        });
    }

    /**
     * Scope to filter products that are available (stock > 0).
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }
}
