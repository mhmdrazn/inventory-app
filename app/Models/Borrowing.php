<?php

namespace App\Models;

use Database\Factories\BorrowingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Borrowing extends Model
{
    /** @use HasFactory<BorrowingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'approved_by',
        'borrower_name',
        'status',
        'borrowed_at',
        'due_at',
        'returned_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'borrowed_at' => 'date',
            'due_at' => 'date',
            'returned_at' => 'date',
        ];
    }

    /**
     * The user who created this borrowing record.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin/manager who approved this borrowing.
     *
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<BorrowingDetail, $this>
     */
    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    /**
     * Scope to filter active borrowings (status = dipinjam).
     *
     * @param  Builder<Borrowing>  $query
     * @return Builder<Borrowing>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Check if this borrowing is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'dipinjam' && $this->due_at->lt(Carbon::today());
    }
}
