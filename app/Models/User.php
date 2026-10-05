<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, HasRoles, Notifiable;

    public const STAFF_ROLES = ['admin', 'lawyer', 'secretary', 'accountant'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'locale',
        'job_title',
        'bar_registration_no',
        'is_active',
        'client_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(self::STAFF_ROLES),
            'portal' => $this->client_id !== null,
            default => false,
        };
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(self::STAFF_ROLES);
    }

    public function isLawyer(): bool
    {
        return $this->hasAnyRole(['admin', 'lawyer']);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function leadCases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'lead_lawyer_id');
    }

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(LegalCase::class, 'case_lawyer');
    }

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class, 'lawyer_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function scopeStaff($query)
    {
        return $query->role(self::STAFF_ROLES);
    }

    public function scopeLawyers($query)
    {
        return $query->role(['admin', 'lawyer']);
    }
}
