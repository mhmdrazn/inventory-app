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

    /**
     * Generate the next unique code for a product in the given category.
     *
     * Format: INV-{CAT3}-{sequence padded to 3 digits}, e.g. INV-ELE-014.
     * Falls back to INV-GEN-### when no category is provided.
     */
    public static function generateCode(?int $categoryId): string
    {
        $prefix = 'INV-GEN';

        if ($categoryId !== null) {
            $category = Category::find($categoryId);
            if ($category) {
                $prefix = 'INV-'.strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name) ?: 'GEN', 0, 3));
            }
        }

        $lastCode = self::where('code', 'like', $prefix.'-%')
            ->orderByDesc('code')
            ->value('code');

        $nextSequence = 1;
        if ($lastCode && preg_match('/-(\d+)$/', $lastCode, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        do {
            $candidate = sprintf('%s-%03d', $prefix, $nextSequence);
            $nextSequence++;
        } while (self::where('code', $candidate)->exists());

        return $candidate;
    }
}
