<?php

namespace App\Models;

use App\Enums\ClientType;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use GeneratesReference, HasFactory, SoftDeletes;

    protected string $referenceColumn = 'code';

    protected string $referencePrefix = 'CL';

    protected $fillable = [
        'code', 'type', 'name', 'name_en', 'national_id', 'commercial_register_no', 'tax_number',
        'nationality', 'phone', 'phone_alt', 'email', 'address', 'city', 'occupation',
        'user_id', 'created_by', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => ClientType::class,
            'is_active' => 'boolean',
        ];
    }

    public function portalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function portalAccount(): HasOne
    {
        return $this->hasOne(User::class, 'client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function powersOfAttorney(): HasMany
    {
        return $this->hasMany(PowerOfAttorney::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function feeAgreements(): HasMany
    {
        return $this->hasMany(FeeAgreement::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->invoices()
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->selectRaw('COALESCE(SUM(total - paid_amount), 0) as balance')
            ->value('balance');
    }
}
