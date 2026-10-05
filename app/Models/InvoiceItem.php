<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'description', 'quantity', 'unit_price', 'total'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item): void {
            $item->total = round((float) $item->quantity * (float) $item->unit_price, 2);
        });

        static::saved(fn (InvoiceItem $item) => $item->invoice?->recalculateTotals());
        static::deleted(fn (InvoiceItem $item) => $item->invoice?->recalculateTotals());
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
