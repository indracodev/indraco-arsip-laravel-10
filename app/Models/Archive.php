<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_number',
        'department_id',
        'created_by_user_id',
        'title',
        'period_start_date',
        'period_end_date',
        'period_text',
        'content_description',
        'retention_years',
        'retention_expiry_date',
        'physical_condition',
        'file_path',
        'warehouse_location_id',
        'status',
        'rejection_note',
    ];

    protected $casts = [
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'retention_expiry_date' => 'date',
        'retention_years' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_id');
    }

    public function entryLogs(): HasMany
    {
        return $this->hasMany(WarehouseEntryLog::class);
    }

    public function borrowingLogs(): HasMany
    {
        return $this->hasMany(BorrowingLog::class);
    }

    public function latestBorrowingLog(): HasOne
    {
        return $this->hasOne(BorrowingLog::class)->latestOfMany();
    }

    public function destructionLog(): HasOne
    {
        return $this->hasOne(DestructionLog::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft Usulan',
            'pending_verification' => 'Menunggu Verifikasi',
            'approved_booked' => 'Booking Disetujui',
            'in_warehouse' => 'Tersimpan di Gudang',
            'borrowed' => 'Sedang Dipinjam',
            'pending_destruction' => 'Antrean Pemusnahan',
            'destroyed' => 'Telah Dimusnahkan',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-700 border-gray-200',
            'pending_verification' => 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse',
            'approved_booked' => 'bg-blue-100 text-blue-800 border-blue-200',
            'in_warehouse' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'borrowed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'pending_destruction' => 'bg-orange-100 text-orange-800 border-orange-300',
            'destroyed' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->retention_expiry_date || $this->status === 'destroyed') {
            return false;
        }

        $now = Carbon::now();
        $expiry = Carbon::parse($this->retention_expiry_date);

        return $expiry->diffInDays($now, false) >= -90; // Expiry within 90 days or passed
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->retention_expiry_date || $this->status === 'destroyed') {
            return false;
        }

        return Carbon::parse($this->retention_expiry_date)->isPast();
    }
}
