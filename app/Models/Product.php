<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

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
     * Resolve `image` to a full displayable URL:
     * - remote http(s) URLs are returned unchanged
     * - local paths go through Supabase Storage public URL
     * - empty stays null
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->image) {
                    return null;
                }
                if (Str::startsWith($this->image, ['http://', 'https://'])) {
                    return $this->image;
                }

                return Storage::disk('supabase')->url($this->image);
            },
        );
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

        $term = '%'.mb_strtolower($search).'%';

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(name) LIKE ?', [$term])
                ->orWhereRaw('LOWER(code) LIKE ?', [$term]);
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
