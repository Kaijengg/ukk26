<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'book_id', 'processed_by', 'loan_date', 'due_date', 'returned_at', 'status', 'notes'];
    protected $casts = ['loan_date' => 'date', 'due_date' => 'date', 'returned_at' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (Loan $loan) {
            if ($loan->status !== 'dipinjam') return;
            DB::transaction(function () use ($loan) {
                $book = Book::whereKey($loan->book_id)->lockForUpdate()->first();
                if (! $book || $book->available_stock < 1) throw ValidationException::withMessages(['book_id' => 'Buku ini sedang tidak tersedia.']);
                $book->decrement('available_stock');
            });
        });
        static::updating(function (Loan $loan) {
            if (! $loan->isDirty('status')) return;
            DB::transaction(function () use ($loan) {
                $book = Book::whereKey($loan->book_id)->lockForUpdate()->firstOrFail();
                if ($loan->status === 'dikembalikan' && $loan->getOriginal('status') === 'dipinjam') {
                    $book->increment('available_stock');
                    $loan->returned_at = $loan->returned_at ?: now()->toDateString();
                } elseif ($loan->status === 'dipinjam' && $loan->getOriginal('status') === 'dikembalikan') {
                    if ($book->available_stock < 1) throw ValidationException::withMessages(['status' => 'Stok buku tidak tersedia untuk membuka kembali transaksi.']);
                    $book->decrement('available_stock');
                    $loan->returned_at = null;
                }
            });
        });
        static::deleting(function (Loan $loan) {
            if ($loan->status === 'dipinjam') Book::whereKey($loan->book_id)->increment('available_stock');
        });
    }

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
    public function getIsOverdueAttribute(): bool { return $this->status === 'dipinjam' && $this->due_date->lt(today()); }
}
