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

    public const ROLE_KANTOR = 'kantor';
    public const ROLE_LOGISTIK = 'logistik';
    public const ROLE_LAPANGAN = 'lapangan';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'identity_number',
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

    public function isFullAccess(): bool
    {
        return $this->role === self::ROLE_KANTOR;
    }

    public function isMediumAccess(): bool
    {
        return $this->role === self::ROLE_LOGISTIK;
    }

    public function isLowAccess(): bool
    {
        return $this->role === self::ROLE_LAPANGAN;
    }

    public function canManageUsers(): bool
    {
        return $this->isFullAccess();
    }

    public function canManageBranches(): bool
    {
        return $this->isFullAccess();
    }

    public function canManageItems(): bool
    {
        return $this->isFullAccess();
    }

    public function canManagePrices(): bool
    {
        return $this->isFullAccess();
    }

    public function canUseExcelUpload(): bool
    {
        return $this->isFullAccess();
    }

    public function canVerify(): bool
    {
        return $this->isFullAccess();
    }

    public function canAddOfficeNote(): bool
    {
        return $this->isMediumAccess() || $this->isFullAccess();
    }

    public function canEditInformation(): bool
    {
        return $this->isFullAccess();
    }

    public function dashboardRouteName(): string
    {
        return $this->isFullAccess()
            ? 'superadmin.dashboard'
            : 'admin.dashboard';
    }

    public function routePrefix(): string
    {
        return $this->isFullAccess() ? 'superadmin' : 'admin';
    }

    public function panelRouteName(string $route): string
    {
        return "{$this->routePrefix()}.{$route}";
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_KANTOR => 'M. Kantor',
            self::ROLE_LOGISTIK => 'Officer / M. Logistik',
            self::ROLE_LAPANGAN => 'M. Lapangan',
        ];
    }
}
