<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'rack_code',
        'shelf_code',
        'box_capacity',
        'current_box_count',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }

    public function getFullLocationAttribute(): string
    {
        $whName = $this->warehouse ? $this->warehouse->name : 'Gudang';
        return "{$whName} - {$this->rack_code} / {$this->shelf_code}";
    }

    public function getCapacityPercentageAttribute(): float
    {
        if ($this->box_capacity <= 0) return 0;
        return round(($this->current_box_count / $this->box_capacity) * 100, 1);
    }
}
