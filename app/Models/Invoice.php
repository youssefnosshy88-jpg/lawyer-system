<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use GeneratesReference, HasFactory, SoftDeletes;

    protected string $referenceColumn = 'number';

    protected string $referencePrefix = 'INV';

    protected $fillable = [
        'number', 'client_id', 'legal_case_id', 'company_id', 'fee_agreement_id', 'status', 'issued_at', 'due_at',
        'subtotal', 'discount', 'tax_rate', 'tax_amount', 'total', 'paid_amount', 'currency', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issued_at' => 'date',
            'due_at' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice): void {
            $invoice->created_by ??= auth()->id();
            $invoice->currency ??= config('firm.currency');
        });
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

    public function feeAgreement(): BelongsTo
    {
        return $this->belongsTo(FeeAgreement::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeOutstanding(Builder $query): Builder
    {
        return $query->whereIn('status', [
            InvoiceStatus::SENT->value,
            InvoiceStatus::PARTIALLY_PAID->value,
            InvoiceStatus::OVERDUE->value,
        ]);
    }

    public function getBalanceAttribute(): float
    {
        return (float) max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('total');
        $taxable = max(0, $subtotal - (float) $this->discount);
        $tax = round($taxable * ((float) $this->tax_rate / 100), 2);

        $this->subtotal = $subtotal;
        $this->tax_amount = $tax;
        $this->total = $taxable + $tax;
        $this->paid_amount = (float) $this->payments()->sum('amount');
        $this->status = $this->resolveStatus();
        $this->saveQuietly();
    }

    public function resolveStatus(): InvoiceStatus
    {
        if (in_array($this->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED], true)) {
            return $this->status;
        }

        if ((float) $this->total > 0 && (float) $this->paid_amount >= (float) $this->total) {
            return InvoiceStatus::PAID;
        }

        if ((float) $this->paid_amount > 0) {
            return InvoiceStatus::PARTIALLY_PAID;
        }

        if ($this->due_at && $this->due_at->isPast()) {
            return InvoiceStatus::OVERDUE;
        }

        return InvoiceStatus::SENT;
    }
}
