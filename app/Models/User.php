<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class, 'created_by_user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPicDept(): bool
    {
        return $this->role === 'pic_dept';
    }

    public function isPicGudang(): bool
    {
        return $this->role === 'pic_gudang';
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
            'pic_gudang' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Super Admin',
            'pic_gudang' => 'PIC Gudang',
            default => 'PIC Departemen',
        };
    }
}
