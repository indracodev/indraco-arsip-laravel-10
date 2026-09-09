<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestructionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'archive_id',
        'proposed_by_user_id',
        'approved_by_dept_pic_id',
        'bap_number',
        'destruction_date',
        'method',
        'certificate_file',
        'notes',
    ];

    protected $casts = [
        'destruction_date' => 'date',
    ];

    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class);
    }

    public function proposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposed_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_dept_pic_id');
    }
}
