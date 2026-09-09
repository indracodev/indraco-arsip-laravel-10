<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'archive_id',
        'borrower_user_id',
        'pic_gudang_id',
        'request_date',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'purpose',
        'status',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'borrow_date' => 'datetime',
        'expected_return_date' => 'date',
        'actual_return_date' => 'datetime',
    ];

    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_user_id');
    }

    public function picGudang(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_gudang_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'requested' => 'Diajukan',
            'approved' => 'Disetujui',
            'dispatched' => 'Pengeluaran Berkas',
            'returned' => 'Dikembalikan',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'requested' => 'bg-amber-100 text-amber-800 border-amber-200',
            'approved' => 'bg-blue-100 text-blue-800 border-blue-200',
            'dispatched' => 'bg-purple-100 text-purple-800 border-purple-200',
            'returned' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
