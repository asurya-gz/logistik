<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_CABANG = 'admin_cabang';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdLogistics(): HasMany
    {
        return $this->hasMany(Logistics::class, 'created_by');
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class, 'uploaded_by');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'verified_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN_CABANG;
    }

    public function canVerify(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_CABANG, self::ROLE_SUPER_ADMIN], true);
    }

    public function dashboardRouteName(): string
    {
        return $this->isSuperAdmin()
            ? 'superadmin.dashboard'
            : 'admin.dashboard';
    }

    public function routePrefix(): string
    {
        return $this->isSuperAdmin() ? 'superadmin' : 'admin';
    }

    public function panelRouteName(string $route): string
    {
        return "{$this->routePrefix()}.{$route}";
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN_CABANG => 'Admin Cabang',
        ];
    }
}
