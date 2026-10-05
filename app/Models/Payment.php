<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\GeneratesReference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use GeneratesReference, HasFactory;

    protected string $referenceColumn = 'receipt_no';

    protected string $referencePrefix = 'RC';

    protected $fillable = [
        'receipt_no', 'client_id', 'invoice_id', 'amount', 'method', 'paid_at', 'reference', 'file_path', 'notes', 'received_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'method' => PaymentMethod::class,
            'paid_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment): void {
            $payment->received_by ??= auth()->id();

            if ($payment->invoice_id && ! $payment->client_id) {
                $payment->client_id = $payment->invoice?->client_id;
            }
        });

        static::saved(fn (Payment $payment) => $payment->invoice?->recalculateTotals());
        static::deleted(fn (Payment $payment) => $payment->invoice?->recalculateTotals());
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
