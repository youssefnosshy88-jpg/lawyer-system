<?php

namespace App\Models;

use App\Enums\FeeType;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeAgreement extends Model
{
    use GeneratesReference;

    protected string $referencePrefix = 'FA';

    protected $fillable = [
        'reference', 'client_id', 'legal_case_id', 'company_id', 'type', 'total_amount', 'hourly_rate',
        'success_percentage', 'advance_amount', 'installments_count', 'agreed_at', 'file_path', 'terms', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => FeeType::class,
            'total_amount' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'success_percentage' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'agreed_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getInvoicedAmountAttribute(): float
    {
        return (float) $this->invoices()->whereNotIn('status', ['draft', 'cancelled'])->sum('total');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) max(0, (float) $this->total_amount - $this->invoiced_amount);
    }
}
