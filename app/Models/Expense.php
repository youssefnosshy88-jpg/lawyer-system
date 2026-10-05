<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'legal_case_id', 'company_id', 'company_procedure_id', 'client_id', 'category', 'description', 'amount',
        'spent_at', 'billable', 'invoice_id', 'receipt_path', 'paid_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'category' => ExpenseCategory::class,
            'amount' => 'decimal:2',
            'spent_at' => 'date',
            'billable' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Expense $expense): void {
            $expense->paid_by ??= auth()->id();
            $expense->client_id ??= $expense->legalCase?->client_id ?? $expense->company?->client_id;
        });
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(CompanyProcedure::class, 'company_procedure_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
